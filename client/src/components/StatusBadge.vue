<template>
  <span
    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
    :class="badgeClass"
  >
    {{ statusLabel }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true
  }
})

const statusConfig = {
  in_attesa: {
    label: 'In Attesa',
    class: 'bg-yellow-100 text-yellow-800'
  },
  in_lavorazione: {
    label: 'In Lavorazione',
    class: 'bg-blue-100 text-blue-800'
  },
  completato: {
    label: 'Completato',
    class: 'bg-green-100 text-green-800'
  },
  annullato: {
    label: 'Annullato',
    class: 'bg-red-100 text-red-800'
  }
}

const statusLabel = computed(() => {
  return statusConfig[props.status]?.label || props.status
})

const badgeClass = computed(() => {
  return statusConfig[props.status]?.class || 'bg-gray-100 text-gray-800'
})
</script>
