<template>
  <AppLayout>
    <div class="px-4 sm:px-0">
      <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Clienti</h1>
        <div class="mt-4 sm:mt-0">
          <router-link
            to="/customers/create"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
          >
            Nuovo Cliente
          </router-link>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
            <input
              v-model="filters.search"
              @input="applyFilters"
              type="text"
              placeholder="Nome o email..."
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
            <select
              v-model="filters.show_deleted"
              @change="applyFilters"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
            >
              <option value="">Solo Attivi</option>
              <option value="with">Tutti</option>
              <option value="only">Solo Eliminati</option>
            </select>
          </div>

          <div class="flex items-end">
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

        <div v-else-if="customers.length === 0" class="p-6 text-center">
          <span class="text-gray-500">Nessun cliente presente</span>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nome
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Telefono
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Indirizzo
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
              <tr v-for="customer in customers" :key="customer.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ customer.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ customer.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ customer.phone }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ customer.address }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span
                    v-if="customer.is_deleted"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
                  >
                    Eliminato
                  </span>
                  <span
                    v-else
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                  >
                    Attivo
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <router-link
                    v-if="!customer.is_deleted"
                    :to="`/customers/${customer.id}/edit`"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Modifica
                  </router-link>
                  <button
                    v-if="!customer.is_deleted"
                    @click="confirmDelete(customer)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Elimina
                  </button>
                  <button
                    v-if="customer.is_deleted"
                    @click="confirmRestore(customer)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Ripristina
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

    <!-- Delete/Restore Modals (continua...) -->
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/components/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { customerService } from '@/api/customers'

const customers = ref([])
const loading = ref(true)

const filters = ref({
  search: '',
  show_deleted: ''
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})

const loadCustomers = async (page = 1) => {
  try {
    loading.value = true
    const response = await customerService.getAll(page, filters.value)
    customers.value = response.data
    pagination.value = {
      current_page: response.meta.current_page,
      last_page: response.meta.last_page,
      per_page: response.meta.per_page,
      total: response.meta.total,
      from: response.meta.from,
      to: response.meta.to
    }
  } catch (error) {
    console.error('Failed to load customers:', error)
  } finally {
    loading.value = false
  }
}

const loadPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadCustomers(page)
  }
}

const applyFilters = () => {
  loadCustomers(1)
}

const clearFilters = () => {
  filters.value = { search: '', show_deleted: '' }
  loadCustomers(1)
}

const confirmDelete = (customer) => {
  if(confirm(`Eliminare il cliente ${customer.name}? L'azione può essere annullata.`)) {
    deleteCustomer(customer.id)
  }
}

const deleteCustomer = async (id) => {
  try {
    await customerService.delete(id)
    loadCustomers(pagination.value.current_page)
  } catch (error) {
    alert(error.response?.data?.message || 'Errore durante l\'eliminazione del cliente')
  }
}

const confirmRestore = (customer) => {
  if(confirm(`Ripristinare il cliente ${customer.name}?`)) {
    restoreCustomer(customer.id)
  }
}

const restoreCustomer = async (id) => {
  try {
    await customerService.restore(id)
    loadCustomers(pagination.value.current_page)
  } catch (error) {
    alert(error.response?.data?.message || 'Errore durante il ripristino del cliente')
  }
}

onMounted(() => {
  loadCustomers()
})
</script>
