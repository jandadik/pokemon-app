<template>
    <div 
        class="poke-image-container"
        :class="containerClasses"
        :style="containerStyle"
    >
        <img 
            v-if="imageData?.url"
            :src="currentImageUrl"
            :alt="altText"
            :class="imageClasses"
            @load="onImageLoad"
            @error="onImageError"
            :loading="lazy ? 'lazy' : 'eager'"
        />
        
        <!-- Loading placeholder -->
        <div 
            v-else-if="isLoading" 
            class="poke-image-loading"
            :style="containerStyle"
        >
            <v-skeleton-loader
                v-if="showSkeleton"
                type="image"
                :aspect-ratio="aspectRatio"
                class="rounded"
            />
            <div v-else class="loading-shimmer rounded" />
        </div>
        
        <!-- Error/placeholder state -->
        <div 
            v-else
            class="poke-image-placeholder"
            :style="containerStyle"
        >
            <img 
                src="/images/placeholder.jpg"
                :alt="altText"
                :class="imageClasses"
                loading="eager"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useImageStore } from '@/stores/imageStore'

const props = defineProps({
    // Required
    cardId: {
        type: String,
        required: true
    },
    
    // Image size
    size: {
        type: String,
        default: 'small',
        validator: (value) => ['small', 'large'].includes(value)
    },
    
    // Display options
    aspectRatio: {
        type: [Number, String],
        default: 0.7 // Pokemon card aspect ratio
    },
    
    lazy: {
        type: Boolean,
        default: true
    },
    
    // Styling
    width: {
        type: [Number, String],
        default: null
    },
    
    height: {
        type: [Number, String],
        default: null
    },
    
    cover: {
        type: Boolean,
        default: true
    },
    
    rounded: {
        type: Boolean,
        default: true
    },
    
    // Loading behavior
    eager: {
        type: Boolean,
        default: false
    },
    
    showSkeleton: {
        type: Boolean,
        default: true
    },
    
    // Bulk loading
    queueLoad: {
        type: Boolean,
        default: false
    },
    
    // Alt text
    alt: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['load', 'error', 'loading'])

// Store
const imageStore = useImageStore()

// State
const imageLoaded = ref(false)
const imageError = ref(false)
const currentImageUrl = ref(null)

// Computed
const imageData = computed(() => {
    return imageStore.getImageData(props.cardId, props.size)
})

const isLoading = computed(() => {
    return imageStore.isLoading(props.cardId, props.size) || 
           (!imageData.value && !imageError.value)
})

const isCached = computed(() => {
    return imageStore.isCached(props.cardId, props.size)
})

const altText = computed(() => {
    return props.alt || `Pokemon card ${props.cardId}`
})

const containerStyle = computed(() => {
    const style = {}
    
    if (props.width) {
        style.width = typeof props.width === 'number' ? `${props.width}px` : props.width
    }
    
    if (props.height) {
        style.height = typeof props.height === 'number' ? `${props.height}px` : props.height
    } else if (props.aspectRatio && props.width) {
        const width = typeof props.width === 'number' ? props.width : parseInt(props.width)
        style.height = `${width / props.aspectRatio}px`
    }
    
    if (props.aspectRatio && !props.height && !props.width) {
        style.aspectRatio = props.aspectRatio
    }
    
    return style
})

const containerClasses = computed(() => {
    return [
        'poke-image-container',
        {
            'rounded': props.rounded,
            'loading': isLoading.value,
            'loaded': imageLoaded.value,
            'error': imageError.value
        }
    ]
})

const imageClasses = computed(() => {
    return [
        'poke-image',
        {
            'object-cover': props.cover,
            'object-contain': !props.cover,
            'rounded': props.rounded
        }
    ]
})

// Methods
const loadImage = async () => {
    if (!props.cardId) return
    
    emit('loading', true)
    
    try {
        let data
        
        if (props.queueLoad && !props.eager) {
            // Queue for bulk loading (debounced)
            imageStore.queueImageLoad(props.cardId, props.size)
            
            // Wait for it to be loaded
            data = await waitForImageData()
        } else {
            // Load immediately
            data = await imageStore.loadImageData(props.cardId, props.size)
        }
        
        if (data?.url) {
            currentImageUrl.value = data.url
        }
    } catch (error) {
        console.error('Failed to load image:', error)
        imageError.value = true
        emit('error', error)
    } finally {
        emit('loading', false)
    }
}

const waitForImageData = async () => {
    let attempts = 0
    const maxAttempts = 100 // 10 seconds max wait
    
    while (attempts < maxAttempts) {
        const data = imageStore.getImageData(props.cardId, props.size)
        if (data) {
            return data
        }
        
        await new Promise(resolve => setTimeout(resolve, 100))
        attempts++
    }
    
    // Fallback to direct load if queue takes too long
    return await imageStore.loadImageData(props.cardId, props.size)
}

const onImageLoad = (event) => {
    imageLoaded.value = true
    imageError.value = false
    emit('load', event)
}

const onImageError = (event) => {
    imageError.value = true
    
    // Try fallback URL if available
    if (imageData.value?.fallback && currentImageUrl.value !== imageData.value.fallback) {
        currentImageUrl.value = imageData.value.fallback
        return
    }
    
    // Final fallback to placeholder
    currentImageUrl.value = '/images/placeholder.jpg'
    emit('error', event)
}

// Watchers
watch(() => props.cardId, () => {
    if (props.cardId) {
        imageLoaded.value = false
        imageError.value = false
        currentImageUrl.value = null
        loadImage()
    }
}, { immediate: true })

watch(() => props.size, () => {
    if (props.cardId) {
        imageLoaded.value = false
        imageError.value = false
        currentImageUrl.value = null
        loadImage()
    }
})

watch(imageData, (newData) => {
    if (newData?.url && newData.url !== currentImageUrl.value) {
        currentImageUrl.value = newData.url
    }
}, { immediate: true })

// Lifecycle
onMounted(() => {
    if (props.cardId && !isCached.value) {
        loadImage()
    } else if (imageData.value?.url) {
        currentImageUrl.value = imageData.value.url
    }
})

// Intersection Observer for lazy loading
let observer = null

onMounted(() => {
    if (props.lazy && !props.eager) {
        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isCached.value && !isLoading.value) {
                    loadImage()
                    observer?.unobserve(entry.target)
                }
            })
        }, {
            rootMargin: '50px' // Start loading 50px before entering viewport
        })
        
        observer.observe(document.querySelector('.poke-image-container'))
    }
})

onUnmounted(() => {
    if (observer) {
        observer.disconnect()
    }
})
</script>

<style scoped>
.poke-image-container {
    position: relative;
    overflow: hidden;
    background-color: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.poke-image {
    width: 100%;
    height: 100%;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.poke-image-loading,
.poke-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    width: 100%;
    height: 100%;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

.poke-image-container.loading .poke-image {
    opacity: 0.7;
}

.poke-image-container.loaded .poke-image {
    opacity: 1;
}

.poke-image-container.error .poke-image {
    opacity: 0.5;
}

/* Responsive behavior */
@media (max-width: 768px) {
    .poke-image-container {
        background-color: #fafafa;
    }
}
</style> 