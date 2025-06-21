<template>
    <div class="skeleton-loader">
        <!-- Grid skeleton -->
        <div v-if="type === 'grid'" class="skeleton-grid">
            <div 
                v-for="n in count" 
                :key="n" 
                class="skeleton-card"
            >
                <div class="skeleton-image"></div>
                <div class="skeleton-content">
                    <div class="skeleton-line long"></div>
                    <div class="skeleton-line medium"></div>
                    <div class="skeleton-line short"></div>
                </div>
            </div>
        </div>

        <!-- List skeleton -->
        <div v-else-if="type === 'list'" class="skeleton-list">
            <div 
                v-for="n in count" 
                :key="n" 
                class="skeleton-list-item"
            >
                <div class="skeleton-avatar"></div>
                <div class="skeleton-list-content">
                    <div class="skeleton-line long"></div>
                    <div class="skeleton-line medium"></div>
                </div>
                <div class="skeleton-actions">
                    <div class="skeleton-button"></div>
                    <div class="skeleton-button"></div>
                </div>
            </div>
        </div>

        <!-- Stats skeleton -->
        <div v-else-if="type === 'stats'" class="skeleton-stats">
            <div class="skeleton-stats-row">
                <div 
                    v-for="n in 4" 
                    :key="n" 
                    class="skeleton-stat-card"
                >
                    <div class="skeleton-stat-icon"></div>
                    <div class="skeleton-stat-content">
                        <div class="skeleton-line short"></div>
                        <div class="skeleton-line long"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters skeleton -->
        <div v-else-if="type === 'filters'" class="skeleton-filters">
            <div class="skeleton-search"></div>
            <div class="skeleton-filter-row">
                <div class="skeleton-select"></div>
                <div class="skeleton-select"></div>
                <div class="skeleton-select"></div>
                <div class="skeleton-button-group"></div>
            </div>
        </div>

        <!-- Generic content skeleton -->
        <div v-else class="skeleton-content">
            <div class="skeleton-line long"></div>
            <div class="skeleton-line medium"></div>
            <div class="skeleton-line short"></div>
            <div class="skeleton-line long"></div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'content',
        validator: (value) => ['grid', 'list', 'stats', 'filters', 'content'].includes(value)
    },
    count: {
        type: Number,
        default: 12
    }
})
</script>

<style scoped>
.skeleton-loader {
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Grid Skeleton */
.skeleton-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}

.skeleton-card {
    background: #f5f5f5;
    border-radius: 12px;
    overflow: hidden;
}

.skeleton-image {
    aspect-ratio: 0.7;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

.skeleton-content {
    padding: 12px;
}

/* List Skeleton */
.skeleton-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.skeleton-list-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #f5f5f5;
    border-radius: 12px;
}

.skeleton-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    flex-shrink: 0;
}

.skeleton-list-content {
    flex: 1;
}

.skeleton-actions {
    display: flex;
    gap: 8px;
}

.skeleton-button {
    width: 60px;
    height: 32px;
    border-radius: 6px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

/* Stats Skeleton */
.skeleton-stats {
    margin-bottom: 24px;
}

.skeleton-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.skeleton-stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: #f5f5f5;
    border-radius: 12px;
}

.skeleton-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    flex-shrink: 0;
}

.skeleton-stat-content {
    flex: 1;
}

/* Filters Skeleton */
.skeleton-filters {
    margin-bottom: 24px;
}

.skeleton-search {
    height: 48px;
    background: #f5f5f5;
    border-radius: 12px;
    margin-bottom: 16px;
}

.skeleton-filter-row {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.skeleton-select {
    height: 40px;
    width: 120px;
    background: #f5f5f5;
    border-radius: 8px;
}

.skeleton-button-group {
    height: 40px;
    width: 100px;
    background: #f5f5f5;
    border-radius: 8px;
}

/* Common skeleton elements */
.skeleton-line {
    height: 12px;
    border-radius: 6px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    margin-bottom: 8px;
}

.skeleton-line.short {
    width: 60%;
}

.skeleton-line.medium {
    width: 80%;
}

.skeleton-line.long {
    width: 100%;
}

/* Shimmer animation */
@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Mobile responsiveness */
@media (max-width: 600px) {
    .skeleton-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
    
    .skeleton-filter-row {
        flex-direction: column;
    }
    
    .skeleton-select,
    .skeleton-button-group {
        width: 100%;
    }
    
    .skeleton-stats-row {
        grid-template-columns: 1fr;
    }
}
</style> 