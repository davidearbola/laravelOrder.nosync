import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  // Initialize from localStorage
  const init = () => {
    const storedToken = localStorage.getItem('auth_token')
    const storedUser = localStorage.getItem('user')

    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  const login = async (credentials) => {
    loading.value = true
    error.value = null

    try {
      const response = await authService.login(credentials)

      token.value = response.token
      user.value = response.user

      // Save to localStorage
      localStorage.setItem('auth_token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))

      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Errore durante il login'
      throw err
    } finally {
      loading.value = false
    }
  }

  const register = async (data) => {
    loading.value = true
    error.value = null

    try {
      const response = await authService.register(data)

      token.value = response.token
      user.value = response.user

      // Save to localStorage
      localStorage.setItem('auth_token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))

      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Errore durante la registrazione'
      throw err
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    loading.value = true

    try {
      await authService.logout()
    } catch (err) {
      console.error('Errore durante il logout:', err)
    } finally {
      // Clear state and localStorage
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      loading.value = false
    }
  }

  const fetchUser = async () => {
    try {
      const response = await authService.getUser()
      user.value = response.user
      localStorage.setItem('user', JSON.stringify(response.user))
      return response
    } catch (err) {
      // If fetch fails, clear auth
      await logout()
      throw err
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    init,
    login,
    register,
    logout,
    fetchUser,
  }
})
