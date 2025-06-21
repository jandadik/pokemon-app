<template>
    <v-img
        ref="imageRef"
        :src="isLoaded ? src : placeholder"
        :lazy-src="placeholder"
        :class="['lazy-image', { 'loaded': isLoaded, 'loading': isLoading }]"
        v-bind="$attrs"
        @load="onLoad"
        @error="onError"
    >
        <template #placeholder>
            <div v-if="isLoading" class="d-flex align-center justify-center fill-height">
                <v-progress-circular
                    color="primary"
                    indeterminate
                    size="24"
                />
            </div>
            <div v-else-if="hasError" class="d-flex align-center justify-center fill-height">
                <v-icon color="grey-lighten-1" size="32">mdi-image-broken-variant</v-icon>
            </div>
        </template>
        
        <slot />
    </v-img>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { createLazyObserver } from '@/utils/performance';

const props = defineProps({
    src: {
        type: String,
        required: true
    },
    placeholder: {
        type: String,
        default: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkxvYWRpbmcuLi48L3RleHQ+PC9zdmc+'
    },
    eager: {
        type: Boolean,
        default: false
    }
});

const imageRef = ref(null);
const isLoaded = ref(false);
const isLoading = ref(false);
const hasError = ref(false);
const observer = ref(null);

const onLoad = () => {
    isLoading.value = false;
    isLoaded.value = true;
    hasError.value = false;
};

const onError = () => {
    isLoading.value = false;
    hasError.value = true;
};

const loadImage = () => {
    if (!isLoaded.value && !isLoading.value && !hasError.value) {
        isLoading.value = true;
        // V-img automaticky načte obrázek když se změní src
    }
};

onMounted(() => {
    if (props.eager) {
        // Načti okamžitě
        loadImage();
    } else {
        // Použij Intersection Observer
        observer.value = createLazyObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadImage();
                    observer.value.unobserve(entry.target);
                }
            });
        });

        if (imageRef.value?.$el) {
            observer.value.observe(imageRef.value.$el);
        }
    }
});

onUnmounted(() => {
    if (observer.value) {
        observer.value.disconnect();
    }
});

// Watch for src changes
watch(() => props.src, () => {
    isLoaded.value = false;
    hasError.value = false;
    if (props.eager) {
        loadImage();
    }
});
</script>

<style scoped>
.lazy-image {
    transition: opacity 0.3s ease;
}

.lazy-image.loading {
    opacity: 0.7;
}

.lazy-image.loaded {
    opacity: 1;
}
</style> 