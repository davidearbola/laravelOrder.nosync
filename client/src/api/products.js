import axios from './axios'

export const productService = {
  async getAll(page = 1) {
    const response = await axios.get('/products', { params: { page } })
    return response.data
  },

  async getById(id) {
    const response = await axios.get(`/products/${id}`)
    return response.data
  },

  async create(data) {
    const response = await axios.post('/products', data)
    return response.data
  },

  async update(id, data) {
    const response = await axios.put(`/products/${id}`, data)
    return response.data
  },

  async delete(id) {
    const response = await axios.delete(`/products/${id}`)
    return response.data
  },
}
