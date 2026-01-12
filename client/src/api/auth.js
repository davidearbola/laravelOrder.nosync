import axios from './axios'

export const authService = {
  async register(data) {
    const response = await axios.post('/register', data)
    return response.data
  },

  async login(credentials) {
    const response = await axios.post('/login', credentials)
    return response.data
  },

  async logout() {
    const response = await axios.post('/logout')
    return response.data
  },

  async getUser() {
    const response = await axios.get('/user')
    return response.data
  },
}
