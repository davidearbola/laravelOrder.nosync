<template>
  <AppLayout>
    <div class="px-4 sm:px-0">
      <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-500 truncate">
                  Ordini Totali
                </div>
                <div class="mt-1 text-3xl font-semibold text-gray-900">
                  {{ stats.total || 0 }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-500 truncate">
                  In Attesa
                </div>
                <div class="mt-1 text-3xl font-semibold text-yellow-600">
                  {{ stats.in_attesa || 0 }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-500 truncate">
                  In Lavorazione
                </div>
                <div class="mt-1 text-3xl font-semibold text-blue-600">
                  {{ stats.in_lavorazione || 0 }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-500 truncate">
                  Completati
                </div>
                <div class="mt-1 text-3xl font-semibold text-green-600">
                  {{ stats.completato || 0 }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Azioni Rapide</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <router-link
            to="/orders/create"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
          >
            Nuovo Ordine
          </router-link>
          <router-link
            to="/customers/create"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
          >
            Nuovo Cliente
          </router-link>
          <router-link
            to="/products/create"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
          >
            Nuovo Prodotto
          </router-link>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="mt-8 bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Ordini Recenti</h2>
        <div v-if="loading" class="text-center py-4">
          <span class="text-gray-500">Caricamento...</span>
        </div>
        <div v-else-if="recentOrders.length === 0" class="text-center py-4">
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
              <tr v-for="order in recentOrders" :key="order.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ order.order_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ order.customer?.name || 'Cliente eliminato' }}
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
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <router-link
                    :to="`/orders/${order.id}`"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Dettagli
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/components/AppLayout.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import { orderService } from '@/api/orders'

const stats = ref({
  total: 0,
  in_attesa: 0,
  in_lavorazione: 0,
  completato: 0,
  annullato: 0
})

const recentOrders = ref([])
const loading = ref(true)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('it-IT')
}

const loadDashboard = async () => {
  try {
    loading.value = true
    const response = await orderService.getAll(1)

    recentOrders.value = response.data.slice(0, 5)

    // Calculate stats from all orders
    const allOrders = response.data
    stats.value = {
      total: allOrders.length,
      in_attesa: allOrders.filter(o => o.status === 'in_attesa').length,
      in_lavorazione: allOrders.filter(o => o.status === 'in_lavorazione').length,
      completato: allOrders.filter(o => o.status === 'completato').length,
      annullato: allOrders.filter(o => o.status === 'annullato').length,
    }
  } catch (error) {
    console.error('Failed to load dashboard:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDashboard()
})
</script>
