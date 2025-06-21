/**
 * Performance utilities for throttling, debouncing, and lazy loading
 */
import { ref } from 'vue';

/**
 * Throttle function - limits function execution to once per specified interval
 * @param {Function} func - Function to throttle
 * @param {number} limit - Time limit in milliseconds
 * @returns {Function} Throttled function
 */
export function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Debounce function - delays function execution until after specified delay
 * @param {Function} func - Function to debounce
 * @param {number} delay - Delay in milliseconds
 * @returns {Function} Debounced function
 */
export function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Intersection Observer for lazy loading
 * @param {Function} callback - Callback when element enters viewport
 * @param {Object} options - Intersection Observer options
 * @returns {IntersectionObserver} Observer instance
 */
export function createLazyObserver(callback, options = {}) {
    const defaultOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
    };

    return new IntersectionObserver(callback, { ...defaultOptions, ...options });
}

/**
 * Composable for lazy loading images
 * @returns {Object} Lazy loading utilities
 */
export function useLazyLoading() {
    const observer = ref(null);
    const loadedImages = new Set();

    const initLazyLoading = () => {
        if (!observer.value) {
            observer.value = createLazyObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src;
                        
                        if (src && !loadedImages.has(src)) {
                            img.src = src;
                            img.classList.remove('lazy');
                            img.classList.add('loaded');
                            loadedImages.add(src);
                            observer.value.unobserve(img);
                        }
                    }
                });
            });
        }
    };

    const observeImage = (element) => {
        if (observer.value && element) {
            observer.value.observe(element);
        }
    };

    const cleanup = () => {
        if (observer.value) {
            observer.value.disconnect();
            observer.value = null;
        }
        loadedImages.clear();
    };

    return {
        initLazyLoading,
        observeImage,
        cleanup
    };
}

/**
 * Scroll performance utilities
 */
export function useScrollPerformance() {
    const scrollCallbacks = new Set();
    let isScrolling = false;

    const throttledScrollHandler = throttle(() => {
        scrollCallbacks.forEach(callback => callback());
        isScrolling = false;
    }, 16); // ~60fps

    const addScrollListener = (callback) => {
        if (scrollCallbacks.size === 0) {
            window.addEventListener('scroll', throttledScrollHandler, { passive: true });
        }
        scrollCallbacks.add(callback);
    };

    const removeScrollListener = (callback) => {
        scrollCallbacks.delete(callback);
        if (scrollCallbacks.size === 0) {
            window.removeEventListener('scroll', throttledScrollHandler);
        }
    };

    const cleanup = () => {
        scrollCallbacks.clear();
        window.removeEventListener('scroll', throttledScrollHandler);
    };

    return {
        addScrollListener,
        removeScrollListener,
        cleanup
    };
} 