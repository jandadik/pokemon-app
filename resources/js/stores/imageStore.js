import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

export const useImageStore = defineStore('images', {
    state: () => ({
        // Permanent cache - data se nikdy nezmění
        imageCache: new Map(),
        
        // Loading states
        loadingImages: new Set(),
        
        // Bulk loading queue
        bulkQueue: new Set(),
        bulkTimer: null,
        
        // Settings
        cacheVersion: '1.0',
        maxCacheSize: 1000, // Maximum number of cached images
        initialized: false, // Track if store has been initialized
    }),
    
    getters: {
        /**
         * Get image data from cache
         */
        getImageData: (state) => (cardId, size = 'small') => {
            const key = `${cardId}_${size}`
            return state.imageCache.get(key)
        },
        
        /**
         * Check if image is cached
         */
        isCached: (state) => (cardId, size = 'small') => {
            const key = `${cardId}_${size}`
            return state.imageCache.has(key)
        },
        
        /**
         * Check if image is loading
         */
        isLoading: (state) => (cardId, size = 'small') => {
            const key = `${cardId}_${size}`
            return state.loadingImages.has(key)
        },
        
        /**
         * Get cache statistics
         */
        cacheStats: (state) => ({
            size: state.imageCache.size,
            loading: state.loadingImages.size,
            maxSize: state.maxCacheSize,
            usage: (state.imageCache.size / state.maxCacheSize * 100).toFixed(1) + '%'
        })
    },
    
    actions: {
        /**
         * Initialize store and load persisted cache
         */
        init() {
            if (!this.initialized) {
                this.loadPersistedCache()
                this.initialized = true
            }
        },
        
        /**
         * Load single image data
         */
        async loadImageData(cardId, size = 'small') {
            this.init() // Ensure store is initialized
            const key = `${cardId}_${size}`
            
            // Already cached
            if (this.imageCache.has(key)) {
                return this.imageCache.get(key)
            }
            
            // Already loading
            if (this.loadingImages.has(key)) {
                return this.waitForLoading(key)
            }
            
            this.loadingImages.add(key)
            
            try {
                const response = await this.fetchImageData([cardId], size)
                const imageData = response[cardId]
                
                if (imageData) {
                    this.cacheImageData(key, imageData)
                    return imageData
                }
                
                return this.getPlaceholderData()
            } catch (error) {
                console.error('Failed to load image data:', error)
                return this.getPlaceholderData()
            } finally {
                this.loadingImages.delete(key)
            }
        },
        
        /**
         * Load multiple images (bulk)
         */
        async loadBulkImageData(cardIds, size = 'small') {
            this.init() // Ensure store is initialized
            const uncachedIds = cardIds.filter(cardId => {
                const key = `${cardId}_${size}`
                return !this.imageCache.has(key) && !this.loadingImages.has(key)
            })
            
            if (uncachedIds.length === 0) {
                // All cached, return immediately
                const result = {}
                cardIds.forEach(cardId => {
                    const key = `${cardId}_${size}`
                    result[cardId] = this.imageCache.get(key) || this.getPlaceholderData()
                })
                return result
            }
            
            // Mark as loading
            uncachedIds.forEach(cardId => {
                const key = `${cardId}_${size}`
                this.loadingImages.add(key)
            })
            
            try {
                const response = await this.fetchImageData(uncachedIds, size)
                
                // Cache results
                Object.entries(response).forEach(([cardId, imageData]) => {
                    const key = `${cardId}_${size}`
                    this.cacheImageData(key, imageData)
                })
                
                // Return all requested data (cached + new)
                const result = {}
                cardIds.forEach(cardId => {
                    const key = `${cardId}_${size}`
                    result[cardId] = this.imageCache.get(key) || this.getPlaceholderData()
                })
                
                return result
            } catch (error) {
                console.error('Failed to load bulk image data:', error)
                
                // Return placeholders for failed loads
                const result = {}
                cardIds.forEach(cardId => {
                    const key = `${cardId}_${size}`
                    result[cardId] = this.imageCache.get(key) || this.getPlaceholderData()
                })
                return result
            } finally {
                // Clear loading states
                uncachedIds.forEach(cardId => {
                    const key = `${cardId}_${size}`
                    this.loadingImages.delete(key)
                })
            }
        },
        
        /**
         * Queue image for bulk loading (debounced)
         */
        queueImageLoad(cardId, size = 'small') {
            this.init() // Ensure store is initialized
            const key = `${cardId}_${size}`
            
            // Already cached or loading
            if (this.imageCache.has(key) || this.loadingImages.has(key)) {
                return
            }
            
            this.bulkQueue.add(key)
            
            // Debounce bulk loading
            if (this.bulkTimer) {
                clearTimeout(this.bulkTimer)
            }
            
            this.bulkTimer = setTimeout(() => {
                this.processBulkQueue()
            }, 50) // 50ms debounce
        },
        
        /**
         * Process bulk loading queue
         */
        async processBulkQueue() {
            if (this.bulkQueue.size === 0) return
            
            const queue = Array.from(this.bulkQueue)
            this.bulkQueue.clear()
            
            // Group by size
            const bySize = {}
            queue.forEach(key => {
                const [cardId, size] = key.split('_')
                if (!bySize[size]) bySize[size] = []
                bySize[size].push(cardId)
            })
            
            // Load each size group
            const promises = Object.entries(bySize).map(([size, cardIds]) => 
                this.loadBulkImageData(cardIds, size)
            )
            
            await Promise.all(promises)
        },
        
        /**
         * Fetch image data from backend
         */
        async fetchImageData(cardIds, size = 'small') {
            try {
                const response = await fetch('/images/bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        card_ids: cardIds,
                        size: size
                    })
                })
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`)
                }
                
                const data = await response.json()
                return data.imageData || {}
            } catch (error) {
                console.error('Failed to fetch image data:', error)
                throw new Error('Failed to fetch image data: ' + error.message)
            }
        },
        
        /**
         * Cache image data with size management
         */
        cacheImageData(key, imageData) {
            // Manage cache size
            if (this.imageCache.size >= this.maxCacheSize) {
                // Remove oldest entries (simple FIFO)
                const oldestKeys = Array.from(this.imageCache.keys()).slice(0, 100)
                oldestKeys.forEach(oldKey => this.imageCache.delete(oldKey))
            }
            
            this.imageCache.set(key, {
                ...imageData,
                cached_at: new Date().toISOString()
            })
            
            // Persist to localStorage (with size limit)
            this.persistCache()
        },
        
        /**
         * Wait for loading to complete
         */
        async waitForLoading(key) {
            return new Promise((resolve) => {
                const checkLoading = () => {
                    if (!this.loadingImages.has(key)) {
                        resolve(this.imageCache.get(key) || this.getPlaceholderData())
                    } else {
                        setTimeout(checkLoading, 10)
                    }
                }
                checkLoading()
            })
        },
        
        /**
         * Get placeholder data
         */
        getPlaceholderData() {
            return {
                url: '/images/placeholder.jpg',
                fallback: '/images/placeholder.jpg',
                cardId: null,
                size: 'small',
                cached_at: new Date().toISOString()
            }
        },
        
        /**
         * Persist cache to localStorage
         */
        persistCache() {
            try {
                const cacheData = {
                    version: this.cacheVersion,
                    data: Object.fromEntries(this.imageCache),
                    timestamp: Date.now()
                }
                
                const serialized = JSON.stringify(cacheData)
                
                // Check size (max 5MB for localStorage)
                if (serialized.length < 5 * 1024 * 1024) {
                    localStorage.setItem('pokemon_image_cache', serialized)
                }
            } catch (error) {
                console.warn('Failed to persist image cache:', error)
            }
        },
        
        /**
         * Load cache from localStorage
         */
        loadPersistedCache() {
            try {
                const cached = localStorage.getItem('pokemon_image_cache')
                if (!cached) return
                
                const cacheData = JSON.parse(cached)
                
                // Check version compatibility
                if (cacheData.version !== this.cacheVersion) {
                    localStorage.removeItem('pokemon_image_cache')
                    return
                }
                
                // Check age (max 7 days)
                const age = Date.now() - cacheData.timestamp
                if (age > 7 * 24 * 60 * 60 * 1000) {
                    localStorage.removeItem('pokemon_image_cache')
                    return
                }
                
                // Restore cache
                this.imageCache = new Map(Object.entries(cacheData.data))
            } catch (error) {
                console.warn('Failed to load persisted image cache:', error)
                localStorage.removeItem('pokemon_image_cache')
            }
        },
        
        /**
         * Clear all cache
         */
        clearCache() {
            this.imageCache.clear()
            this.loadingImages.clear()
            this.bulkQueue.clear()
            localStorage.removeItem('pokemon_image_cache')
        },
        
        /**
         * Preload images for collection
         */
        async preloadCollectionImages(collectionId, size = 'small') {
            // This would be called from backend via Inertia props
            // Implementation depends on how collection data is structured
        }
    }
}) 