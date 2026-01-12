<template>
  <AppLayout>
    <div class="px-4 sm:px-0">
      <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ordini</h1>
        <div class="mt-4 sm:mt-0">
          <router-link
            to="/orders/create"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
          >
            Nuovo Ordine
          </router-link>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
            <input
              v-model="filters.search"
              @input="applyFilters"
              type="text"
              placeholder="Numero ordine..."
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
            <select
              v-model="filters.status"
              @change="applyFilters"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
            >
              <option value="">Tutti</option>
              <option value="in_attesa">In Attesa</option>
              <option value="in_lavorazione">In Lavorazione</option>
              <option value="completato">Completato</option>
              <option value="annullato">Annullato</option>
            </select>
          </div>
          <div class="sm:col-span-2 flex items-end">
            <button
              @click="clearFilters"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Ripristina Filtri
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg">
        <div v-if="loading" class="p-6 text-center">
          <span class="text-gray-500">Caricamento...</span>
        </div>

        <div v-else-if="orders.length === 0" class="p-6 text-center">
          <span class="text-gray-500">Nessun ordine presente</span>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Numero Ordine
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Data
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Totale
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Stato
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Azioni
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="order in orders" :key="order.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ order.order_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <span v-if="order.customer && !order.customer.is_deleted">
                    {{ order.customer.name }}
                  </span>
                  <span v-else class="text-red-600 italic">
                    Cliente eliminato
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(order.date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  € {{ order.total_amount }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <StatusBadge :status="order.status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <router-link
                    :to="`/orders/${order.id}`"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Dettagli
                  </router-link>
                  <button
                    v-if="order.can_be_modified"
                    @click="confirmDelete(order)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Elimina
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination :pagination="pagination" @page-change="loadPage" />
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Elimina Ordine
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Sei sicuro di voler eliminare l'ordine <strong>{{ orderToDelete?.order_number }}</strong>?
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="deleteOrder"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Elimina
            </button>
            <button
              @click="showDeleteModal = false"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Annulla
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/components/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import { orderService } from '@/api/orders'

const orders = ref([])
const loading = ref(true)
const showDeleteModal = ref(false)
const orderToDelete = ref(null)

const filters = ref({
  search: '',
  status: ''
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('it-IT')
}

const loadOrders = async (page = 1) => {
  try {
    loading.value = true
    const response = await orderService.getAll(page, filters.value)
    orders.value = response.data
    pagination.value = {
      current_page: response.meta.current_page,
      last_page: response.meta.last_page,
      per_page: response.meta.per_page,
      total: response.meta.total,
      from: response.meta.from,
      to: response.meta.to
    }
  } catch (error) {
    console.error('Failed to load orders:', error)
  } finally {
    loading.value = false
  }
}

const loadPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadOrders(page)
  }
}

const applyFilters = () => {
  loadOrders(1)
}

const clearFilters = () => {
  filters.value = { search: '', status: '' }
  loadOrders(1)
}

const confirmDelete = (order) => {
  orderToDelete.value = order
  showDeleteModal.value = true
}

const deleteOrder = async () => {
  try {
    await orderService.delete(orderToDelete.value.id)
    showDeleteModal.value = false
    orderToDelete.value = null
    loadOrders(pagination.value.current_page)
  } catch (error) {
    console.error('Failed to delete order:', error)
    alert(error.response?.data?.message || 'Errore durante l\'eliminazione dell\'ordine')
  }
}

onMounted(() => {
  loadOrders()
})
</script>
