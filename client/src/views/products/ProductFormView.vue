<template>
  <AppLayout>
    <div class="px-4 sm:px-0">
      <div class="mb-6">
        <router-link
          to="/products"
          class="text-sm text-blue-600 hover:text-blue-700"
        >
          ← Torna ai prodotti
        </router-link>
      </div>

      <h1 class="text-2xl font-bold text-gray-900 mb-6">
        {{ isEdit ? 'Modifica Prodotto' : 'Nuovo Prodotto' }}
      </h1>

      <div class="bg-white shadow rounded-lg p-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Code -->
            <div>
              <label for="code" class="block text-sm font-medium text-gray-700">
                Codice <span class="text-red-500">*</span>
              </label>
              <input
                id="code"
                v-model="form.code"
                type="text"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
              <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ errors.code[0] }}</p>
            </div>

            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">
                Nome <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">
              Descrizione
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Price -->
            <div>
              <label for="price" class="block text-sm font-medium text-gray-700">
                Prezzo (€) <span class="text-red-500">*</span>
              </label>
              <input
                id="price"
                v-model="form.price"
                type="number"
                step="0.01"
                min="0"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
              <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price[0] }}</p>
            </div>

            <!-- Stock Quantity -->
            <div>
              <label for="stock_quantity" class="block text-sm font-medium text-gray-700">
                Giacenza <span class="text-red-500">*</span>
              </label>
              <input
                id="stock_quantity"
                v-model="form.stock_quantity"
                type="number"
                min="0"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
              <p v-if="errors.stock_quantity" class="mt-1 text-sm text-red-600">{{ errors.stock_quantity[0] }}</p>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="errors.general" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  {{ errors.general }}
                </h3>
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end space-x-3">
            <router-link
              to="/products"
              class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Annulla
            </router-link>
            <button
              type="submit"
              :disabled="loading"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              {{ loading ? 'Salvataggio...' : 'Salva' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/components/AppLayout.vue'
import { productService } from '@/api/products'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)

const form = ref({
  code: '',
  name: '',
  description: '',
  price: '',
  stock_quantity: 0
})

const errors = ref({})
const loading = ref(false)

const loadProduct = async () => {
  if (!isEdit.value) return

  try {
    const response = await productService.getById(route.params.id)
    form.value = {
      code: response.data.code,
      name: response.data.name,
      description: response.data.description || '',
      price: response.data.price,
      stock_quantity: response.data.stock_quantity
    }
  } catch (error) {
    console.error('Failed to load product:', error)
    errors.value.general = 'Errore durante il caricamento del prodotto'
  }
}

const handleSubmit = async () => {
  errors.value = {}
  loading.value = true

  try {
    if (isEdit.value) {
      await productService.update(route.params.id, form.value)
    } else {
      await productService.create(form.value)
    }
    router.push('/products')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value.general = error.response?.data?.message || 'Errore durante il salvataggio'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadProduct()
})
</script>
