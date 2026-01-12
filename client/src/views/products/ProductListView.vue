<template>
  <AppLayout>
    <div class="px-4 sm:px-0">
      <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Prodotti</h1>
        <div class="mt-4 sm:mt-0">
          <router-link
            to="/products/create"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
          >
            Nuovo Prodotto
          </router-link>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg">
        <div v-if="loading" class="p-6 text-center">
          <span class="text-gray-500">Caricamento...</span>
        </div>

        <div v-else-if="products.length === 0" class="p-6 text-center">
          <span class="text-gray-500">Nessun prodotto presente</span>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Codice
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nome
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Prezzo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Giacenza
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
              <tr v-for="product in products" :key="product.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ product.code }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                  <div class="font-medium">{{ product.name }}</div>
                  <div v-if="product.description" class="text-gray-500 truncate max-w-xs">
                    {{ product.description }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  € {{ product.price }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ product.stock_quantity }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <StockBadge :stockStatus="product.stock_status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <router-link
                    :to="`/products/${product.id}/edit`"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Modifica
                  </router-link>
                  <button
                    @click="confirmDelete(product)"
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
                  Elimina Prodotto
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Sei sicuro di voler eliminare il prodotto <strong>{{ productToDelete?.name }}</strong>?
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="deleteProduct"
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
import StockBadge from '@/components/StockBadge.vue'
import { productService } from '@/api/products'

const products = ref([])
const loading = ref(true)
const showDeleteModal = ref(false)
const productToDelete = ref(null)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})

const loadProducts = async (page = 1) => {
  try {
    loading.value = true
    const response = await productService.getAll(page)
    products.value = response.data
    pagination.value = {
      current_page: response.meta.current_page,
      last_page: response.meta.last_page,
      per_page: response.meta.per_page,
      total: response.meta.total,
      from: response.meta.from,
      to: response.meta.to
    }
  } catch (error) {
    console.error('Failed to load products:', error)
  } finally {
    loading.value = false
  }
}

const loadPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadProducts(page)
  }
}

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const deleteProduct = async () => {
  try {
    await productService.delete(productToDelete.value.id)
    showDeleteModal.value = false
    productToDelete.value = null
    loadProducts(pagination.value.current_page)
  } catch (error) {
    console.error('Failed to delete product:', error)
    alert('Errore durante l\'eliminazione del prodotto')
  }
}

onMounted(() => {
  loadProducts()
})
</script>
