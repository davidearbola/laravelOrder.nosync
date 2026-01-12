<template>
  <div v-if="pagination.total > pagination.per_page" class="bg-gray-50 px-4 py-3 border-t border-gray-200">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="text-center sm:text-left">
        <p class="text-sm text-gray-700">
          Mostrando
          <span class="font-medium">{{ pagination.from }}</span>
          a
          <span class="font-medium">{{ pagination.to }}</span>
          di
          <span class="font-medium">{{ pagination.total }}</span>
          risultati
        </p>
      </div>
      <div class="flex justify-center sm:justify-end">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
          <button
            @click="$emit('page-change', pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Precedente
          </button>
          <button
            @click="$emit('page-change', pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Successivo
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  pagination: {
    type: Object,
    required: true
  }
})

defineEmits(['page-change'])
</script>
