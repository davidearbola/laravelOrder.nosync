import axios from './axios'

export const customerService = {
  async getAll(page = 1, filters = {}) {
    const response = await axios.get('/customers', {
      params: { page, ...filters }
    })
    return response.data
  },

  async getById(id) {
    const response = await axios.get(`/customers/${id}`)
    return response.data
  },

  async create(data) {
    const response = await axios.post('/customers', data)
    return response.data
  },

  async update(id, data) {
    const response = await axios.put(`/customers/${id}`, data)
    return response.data
  },

  async delete(id) {
    const response = await axios.delete(`/customers/${id}`)
    return response.data
  },

  async getOrders(id, page = 1) {
    const response = await axios.get(`/customers/${id}/orders`, { params: { page } })
    return response.data
  },

  async restore(id) {
    const response = await axios.post(`/customers/${id}/restore`)
    return response.data
  },
}
