// TCGPlayerPrices.vue
<template>
  <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <a :href="url" target="_blank" rel="noopener noreferrer" class="text-lg font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-4">
      TCGplayer
      <span class="text-sm font-normal text-gray-500">
        Last updated {{ formatDate(updatedAt) }}
      </span>
    </a>
    
    <!-- Opakovat pro každý typ ceny -->
    <div v-for="(priceData, type) in prices" :key="type" class="mb-6">
      <h3 class="text-lg font-semibold mb-3 capitalize">{{ formatPriceType(type) }}</h3>
      <div class="grid grid-cols-4 gap-4">
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">MARKET</div>
          <div class="text-purple-600 font-bold">
            ${{ formatPrice(Number(priceData.price_market)) }}
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">LOW</div>
          <div class="text-green-600 font-bold">
            ${{ formatPrice(Number(priceData.price_low)) }}
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">MID</div>
          <div class="text-blue-600 font-bold">
            ${{ formatPrice(Number(priceData.price_mid)) }}
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">HIGHT</div>
          <div class="text-red-600 font-bold">
            ${{ formatPrice(Number(priceData.price_high)) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  prices: {
    type: Object,
    required: true
  },
  url: {
    type: String,
    required: true
  },
  updatedAt: {
    type: String,
    default: ''
  }
})
console.log(props.prices)
const formatPrice = (price) => {
  if (typeof price !== 'number' || isNaN(price)) return 'N/A'
  return price.toFixed(2)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('cs-CZ')
}

const formatPriceType = (type) => {
  return type
    .replace(/([A-Z])/g, ' $1')
    .toLowerCase()
    .replace(/^./, str => str.toUpperCase())
}
</script>