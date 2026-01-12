<template>
  <AppLayout>
    <div class="px-4 sm:px-0">
      <div class="mb-6">
        <router-link
          to="/orders"
          class="text-sm text-blue-600 hover:text-blue-700"
        >
          ← Torna agli ordini
        </router-link>
      </div>

      <div v-if="loading" class="text-center py-12">
        <span class="text-gray-500">Caricamento...</span>
      </div>

      <div v-else-if="order" class="space-y-6">
        <!-- Order Header -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">
              Ordine {{ order.order_number }}
            </h1>
            <StatusBadge :status="order.status" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
              <p class="text-sm font-medium text-gray-500">Cliente</p>
              <p class="mt-1 text-sm">
                <span v-if="order.customer && !order.customer.is_deleted" class="text-gray-900">
                  {{ order.customer.name }}
                </span>
                <span v-else-if="order.customer && order.customer.is_deleted">
                  <span class="text-gray-900">{{ order.customer.name }}</span>
                  <span class="text-red-600 italic text-xs ml-1">(Eliminato)</span>
                </span>
                <span v-else class="text-red-600 italic">
                  Cliente non disponibile
                </span>
              </p>
            </div>

            <div>
              <p class="text-sm font-medium text-gray-500">Data</p>
              <p class="mt-1 text-sm text-gray-900">{{ formatDate(order.date) }}</p>
            </div>

            <div>
              <p class="text-sm font-medium text-gray-500">Totale</p>
              <p class="mt-1 text-lg font-bold text-gray-900">€ {{ order.total_amount }}</p>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Prodotti</h2>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Prodotto
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Prezzo Unitario
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Quantità
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Subtotale
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in order.items" :key="item.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ item.product?.name || 'Prodotto non disponibile' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    € {{ item.unit_price }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ item.quantity }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                    € {{ item.subtotal }}
                  </td>
                </tr>
                <tr class="bg-gray-50">
                  <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                    Totale
                  </td>
                  <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                    € {{ order.total_amount }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Status Management -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Gestione Stato</h2>

          <div v-if="!order.can_be_modified" class="rounded-md bg-yellow-50 p-4 mb-4">
            <p class="text-sm text-yellow-800">
              Questo ordine non può essere modificato perché è {{ order.status === 'completato' ? 'completato' : 'annullato' }}.
            </p>
          </div>

          <div v-else class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Cambia Stato
              </label>
              <div class="flex gap-3">
                <select
                  v-model="newStatus"
                  class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
                  <option value="">-- Seleziona nuovo stato --</option>
                  <option
                    v-for="status in availableStatuses"
                    :key="status.value"
                    :value="status.value"
                  >
                    {{ status.label }}
                  </option>
                </select>
                <button
                  @click="updateStatus"
                  :disabled="!newStatus || updatingStatus"
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                >
                  {{ updatingStatus ? 'Aggiornamento...' : 'Aggiorna Stato' }}
                </button>
              </div>
              <p v-if="statusError" class="mt-2 text-sm text-red-600">{{ statusError }}</p>
              <p v-if="statusSuccess" class="mt-2 text-sm text-green-600">{{ statusSuccess }}</p>
            </div>

            <div class="text-sm text-gray-500">
              <p class="font-medium mb-2">Transizioni permesse:</p>
              <ul class="list-disc list-inside space-y-1">
                <li v-if="order.status === 'in_attesa'">
                  Da "In Attesa" puoi passare a "In Lavorazione" o "Annullato"
                </li>
                <li v-if="order.status === 'in_lavorazione'">
                  Da "In Lavorazione" puoi passare a "Completato" o "Annullato"
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
          <router-link
            to="/orders"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Torna alla lista
          </router-link>

          <button
            v-if="order.can_be_modified"
            @click="confirmDelete"
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
          >
            Elimina Ordine
          </button>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <span class="text-red-600">Ordine non trovato</span>
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
                    Sei sicuro di voler eliminare questo ordine? Lo stock dei prodotti verrà ripristinato.
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
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/components/AppLayout.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import { orderService } from '@/api/orders'

const route = useRoute()
const router = useRouter()

const order = ref(null)
const loading = ref(true)
const newStatus = ref('')
const updatingStatus = ref(false)
const statusError = ref('')
const statusSuccess = ref('')
const showDeleteModal = ref(false)

const statusOptions = [
  { value: 'in_attesa', label: 'In Attesa' },
  { value: 'in_lavorazione', label: 'In Lavorazione' },
  { value: 'completato', label: 'Completato' },
  { value: 'annullato', label: 'Annullato' }
]

const statusTransitions = {
  'in_attesa': ['in_lavorazione', 'annullato'],
  'in_lavorazione': ['completato', 'annullato'],
  'completato': [],
  'annullato': []
}

const availableStatuses = computed(() => {
  if (!order.value) return []

  const allowedTransitions = statusTransitions[order.value.status] || []
  return statusOptions.filter(s => allowedTransitions.includes(s.value))
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('it-IT')
}

const loadOrder = async () => {
  try {
    loading.value = true
    const response = await orderService.getById(route.params.id)
    order.value = response.data
  } catch (error) {
    console.error('Failed to load order:', error)
  } finally {
    loading.value = false
  }
}

const updateStatus = async () => {
  statusError.value = ''
  statusSuccess.value = ''
  updatingStatus.value = true

  try {
    await orderService.update(order.value.id, { status: newStatus.value })
    statusSuccess.value = 'Stato aggiornato con successo'
    newStatus.value = ''
    await loadOrder()
  } catch (error) {
    statusError.value = error.response?.data?.message ||
      error.response?.data?.errors?.status?.[0] ||
      'Errore durante l\'aggiornamento dello stato'
  } finally {
    updatingStatus.value = false
  }
}

const confirmDelete = () => {
  showDeleteModal.value = true
}

const deleteOrder = async () => {
  try {
    await orderService.delete(order.value.id)
    router.push('/orders')
  } catch (error) {
    console.error('Failed to delete order:', error)
    alert(error.response?.data?.message || 'Errore durante l\'eliminazione dell\'ordine')
    showDeleteModal.value = false
  }
}

onMounted(() => {
  loadOrder()
})
</script>
