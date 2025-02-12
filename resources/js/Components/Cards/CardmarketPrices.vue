<template>
    <div class="bg-white rounded-lg shadow-sm p-4">
      <a 
        :href="url"
        target="_blank"
        rel="noopener noreferrer"
        class="text-lg font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-4"
      >
        Cardmarket
        <span class="text-sm font-normal text-gray-500">
          Last updated {{ formatDate(updatedAt) }}
        </span>
      </a>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">CM low</div>
          <div class="text-green-600 font-bold">
            {{ formatPrice(Number(prices?.low_price)) }} €
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">CM7</div>
          <div class="text-blue-600 font-bold">
            {{ formatPrice(Number(prices?.avg7)) }} €
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">CM30</div>
          <div class="text-purple-600 font-bold">
            {{ formatPrice(Number(prices?.avg30)) }} €
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">CM trend</div>
          <div class="text-red-600 font-bold">
            {{ formatPrice(Number(prices?.trend_price)) }} €
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">RH CM low</div>
          <div class="text-green-600 font-bold">
            {{ formatPrice(Number(prices?.reverse_holo_low)) }} €
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">RH CM7</div>
          <div class="text-blue-600 font-bold">
            {{ formatPrice(Number(prices?.reverse_holo_avg7)) }} €
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">RH CM30</div>
          <div class="text-purple-600 font-bold">
            {{ formatPrice(Number(prices?.reverse_holo_avg30)) }} €
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-sm text-gray-600">RH CM trend</div>
          <div class="text-red-600 font-bold">
            {{ formatPrice(Number(prices?.reverse_holo_trend)) }} €
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
      required: true
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
  };
  </script>