<template>
  <span
    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
    :class="badgeClass"
  >
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  stockStatus: {
    type: String,
    required: true
  }
})

const stockConfig = {
  available: {
    label: 'Disponibile',
    class: 'bg-green-100 text-green-800'
  },
  low_stock: {
    label: 'Scorte Basse',
    class: 'bg-yellow-100 text-yellow-800'
  },
  out_of_stock: {
    label: 'Esaurito',
    class: 'bg-red-100 text-red-800'
  }
}

const label = computed(() => {
  return stockConfig[props.stockStatus]?.label || props.stockStatus
})

const badgeClass = computed(() => {
  return stockConfig[props.stockStatus]?.class || 'bg-gray-100 text-gray-800'
})
</script>
