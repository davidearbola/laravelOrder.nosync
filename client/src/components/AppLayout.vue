<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <router-link to="/" class="text-xl font-bold text-blue-600">
                Order Tracker
              </router-link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                to="/"
                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Dashboard
              </router-link>
              <router-link
                to="/customers"
                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Clienti
              </router-link>
              <router-link
                to="/products"
                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Prodotti
              </router-link>
              <router-link
                to="/orders"
                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-blue-500 text-gray-900"
              >
                Ordini
              </router-link>
            </div>
          </div>
          <div class="flex items-center">
            <!-- Mobile menu button -->
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Desktop menu -->
            <div class="hidden sm:flex sm:items-center sm:flex-shrink-0">
              <span class="text-sm text-gray-700 mr-4">{{ authStore.user?.name }}</span>
              <button
                @click="handleLogout"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile menu overlay -->
    <div
      v-if="mobileMenuOpen"
      @click="mobileMenuOpen = false"
      class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 sm:hidden"
    ></div>

    <!-- Mobile menu offcanvas -->
    <div
      :class="[
        'fixed top-0 right-0 bottom-0 w-64 bg-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out sm:hidden',
        mobileMenuOpen ? 'translate-x-0' : 'translate-x-full'
      ]"
    >
      <div class="h-full flex flex-col">
        <!-- Menu header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
          <span class="text-lg font-semibold text-gray-900">Menu</span>
          <button
            @click="mobileMenuOpen = false"
            class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- User info -->
        <div class="p-4 border-b border-gray-200">
          <p class="text-sm text-gray-500">Utente</p>
          <p class="text-sm font-medium text-gray-900">{{ authStore.user?.name }}</p>
        </div>

        <!-- Navigation links -->
        <nav class="flex-1 overflow-y-auto p-4">
          <router-link
            to="/"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 mb-1"
            active-class="bg-blue-50 text-blue-700"
          >
            Dashboard
          </router-link>
          <router-link
            to="/customers"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 mb-1"
            active-class="bg-blue-50 text-blue-700"
          >
            Clienti
          </router-link>
          <router-link
            to="/products"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 mb-1"
            active-class="bg-blue-50 text-blue-700"
          >
            Prodotti
          </router-link>
          <router-link
            to="/orders"
            @click="mobileMenuOpen = false"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 mb-1"
            active-class="bg-blue-50 text-blue-700"
          >
            Ordini
          </router-link>
        </nav>

        <!-- Logout button -->
        <div class="p-4 border-t border-gray-200">
          <button
            @click="handleLogout"
            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
          >
            Logout
          </button>
        </div>
      </div>
    </div>

    <!-- Page Content -->
    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>
