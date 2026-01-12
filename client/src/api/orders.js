import axios from './axios'

export const orderService = {
  async getAll(page = 1, filters = {}) {
    const response = await axios.get('/orders', {
      params: { page, ...filters }
    })
    return response.data
  },

  async getById(id) {
    const response = await axios.get(`/orders/${id}`)
    return response.data
  },

  async create(data) {
    const response = await axios.post('/orders', data)
    return response.data
  },

  async update(id, data) {
    const response = await axios.put(`/orders/${id}`, data)
    return response.data
  },

  async delete(id) {
    const response = await axios.delete(`/orders/${id}`)
    return response.data
  },

  async updateStatus(id, status) {
    const response = await axios.patch(`/orders/${id}/status`, { status })
    return response.data
  },
}
