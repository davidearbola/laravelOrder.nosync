import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
      meta: { requiresGuest: true }
    },
    // Registration route disabled - redirect to login
    {
      path: '/register',
      redirect: '/login'
    },
    {
      path: '/',
      name: 'dashboard',
      component: () => import('@/views/DashboardView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/customers',
      name: 'customers',
      component: () => import('@/views/customers/CustomerListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/customers/create',
      name: 'customer-create',
      component: () => import('@/views/customers/CustomerFormView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/customers/:id/edit',
      name: 'customer-edit',
      component: () => import('@/views/customers/CustomerFormView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('@/views/products/ProductListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/products/create',
      name: 'product-create',
      component: () => import('@/views/products/ProductFormView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/products/:id/edit',
      name: 'product-edit',
      component: () => import('@/views/products/ProductFormView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/orders',
      name: 'orders',
      component: () => import('@/views/orders/OrderListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/orders/create',
      name: 'order-create',
      component: () => import('@/views/orders/OrderFormView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/orders/:id',
      name: 'order-detail',
      component: () => import('@/views/orders/OrderDetailView.vue'),
      meta: { requiresAuth: true }
    },
  ]
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Initialize auth store from localStorage if not already done
  if (!authStore.token) {
    authStore.init()
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    // Redirect to login if authentication is required
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    // Redirect to dashboard if already authenticated
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
