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

      <h1 class="text-2xl font-bold text-gray-900 mb-6">Nuovo Ordine</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Form -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Customer Selection -->
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Cliente</h2>
            <div>
              <label for="customer" class="block text-sm font-medium text-gray-700 mb-1">
                Seleziona Cliente <span class="text-red-500">*</span>
              </label>
              <select
                id="customer"
                v-model="form.customer_id"
                required
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option value="">-- Seleziona un cliente --</option>
                <option
                  v-for="customer in customers"
                  :key="customer.id"
                  :value="customer.id"
                  :disabled="customer.is_deleted"
                >
                  {{ customer.name }} {{ customer.is_deleted ? '(Eliminato)' : '' }}
                </option>
              </select>
              <p v-if="errors.customer_id" class="mt-1 text-sm text-red-600">{{ errors.customer_id[0] }}</p>
            </div>
          </div>

          <!-- Product Selection -->
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Prodotti</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Aggiungi Prodotto
                </label>
                <div class="grid grid-cols-1 sm:flex sm:gap-3 gap-3">
                  <select
                    v-model="selectedProduct"
                    class="sm:flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                  >
                    <option value="">-- Seleziona un prodotto --</option>
                    <option
                      v-for="product in availableProducts"
                      :key="product.id"
                      :value="product"
                      :disabled="product.stock_quantity === 0"
                    >
                      {{ product.name }} - € {{ product.price }} (Stock: {{ product.stock_quantity }})
                      {{ product.stock_quantity === 0 ? '- ESAURITO' : '' }}
                    </option>
                  </select>
                  <div class="flex gap-3">
                    <input
                      v-model.number="selectedQuantity"
                      type="number"
                      min="1"
                      placeholder="Quantità"
                      class="flex-1 sm:w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                    />
                    <button
                      @click="addToCart"
                      type="button"
                      class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 whitespace-nowrap"
                    >
                      Aggiungi
                    </button>
                  </div>
                </div>
                <p v-if="stockError" class="mt-1 text-sm text-red-600">{{ stockError }}</p>
              </div>

              <!-- Cart Items -->
              <div v-if="cart.length > 0" class="mt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Prodotti nel carrello</h3>
                <div class="space-y-2">
                  <div
                    v-for="(item, index) in cart"
                    :key="index"
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-md"
                  >
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-900">{{ item.product_name }}</p>
                      <p class="text-sm text-gray-500">€ {{ item.unit_price }} x {{ item.quantity }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                      <p class="text-sm font-medium text-gray-900">
                        € {{ (item.unit_price * item.quantity).toFixed(2) }}
                      </p>
                      <button
                        @click="removeFromCart(index)"
                        type="button"
                        class="text-red-600 hover:text-red-900"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-8 text-gray-500">
                Nessun prodotto nel carrello
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow rounded-lg p-6 sticky top-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Riepilogo Ordine</h2>

            <div class="space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Prodotti</span>
                <span class="text-gray-900">{{ cart.length }}</span>
              </div>

              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Quantità Totale</span>
                <span class="text-gray-900">{{ totalQuantity }}</span>
              </div>

              <div class="border-t border-gray-200 pt-3 mt-3">
                <div class="flex justify-between">
                  <span class="text-base font-medium text-gray-900">Totale</span>
                  <span class="text-base font-medium text-gray-900">€ {{ totalAmount }}</span>
                </div>
              </div>
            </div>

            <!-- Error Message -->
            <div v-if="errors.general" class="mt-4 rounded-md bg-red-50 p-3">
              <p class="text-sm text-red-800">{{ errors.general }}</p>
            </div>

            <div v-if="errors.items" class="mt-4 rounded-md bg-red-50 p-3">
              <p class="text-sm text-red-800">{{ errors.items[0] }}</p>
            </div>

            <button
              @click="submitOrder"
              :disabled="!canSubmit || loading"
              type="button"
              class="mt-6 w-full inline-flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ loading ? 'Creazione...' : 'Crea Ordine' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/components/AppLayout.vue'
import { customerService } from '@/api/customers'
import { productService } from '@/api/products'
import { orderService } from '@/api/orders'

const router = useRouter()

const customers = ref([])
const products = ref([])
const cart = ref([])
const selectedProduct = ref('')
const selectedQuantity = ref(1)
const stockError = ref('')
const errors = ref({})
const loading = ref(false)

const form = ref({
  customer_id: '',
  items: []
})

const availableProducts = computed(() => {
  return products.value.filter(p =>
    !cart.value.some(item => item.product_id === p.id)
  )
})

const totalQuantity = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.quantity, 0)
})

const totalAmount = computed(() => {
  return cart.value.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0).toFixed(2)
})

const canSubmit = computed(() => {
  return form.value.customer_id && cart.value.length > 0
})

const loadCustomers = async () => {
  try {
    const response = await customerService.getAll()
    customers.value = response.data
  } catch (error) {
    console.error('Failed to load customers:', error)
  }
}

const loadProducts = async () => {
  try {
    const response = await productService.getAll()
    products.value = response.data
  } catch (error) {
    console.error('Failed to load products:', error)
  }
}

const addToCart = () => {
  stockError.value = ''

  if (!selectedProduct.value || selectedQuantity.value < 1) {
    stockError.value = 'Seleziona un prodotto e inserisci una quantità valida'
    return
  }

  const product = selectedProduct.value

  if (selectedQuantity.value > product.stock_quantity) {
    stockError.value = `Stock insufficiente. Disponibili: ${product.stock_quantity}`
    return
  }

  cart.value.push({
    product_id: product.id,
    product_name: product.name,
    quantity: selectedQuantity.value,
    unit_price: parseFloat(product.price)
  })

  selectedProduct.value = ''
  selectedQuantity.value = 1
}

const removeFromCart = (index) => {
  cart.value.splice(index, 1)
}

const submitOrder = async () => {
  errors.value = {}
  loading.value = true

  try {
    const orderData = {
      customer_id: form.value.customer_id,
      items: cart.value.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity
      }))
    }

    await orderService.create(orderData)
    router.push('/orders')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value.general = error.response?.data?.message || 'Errore durante la creazione dell\'ordine'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCustomers()
  loadProducts()
})
</script>
