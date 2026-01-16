import api from './axios'

export const borrowApi = {
  getRecords(params) {
    return api.get('/borrow-records', { params })
  },
  getRecord(id) {
    return api.get(`/borrow-records/${id}`)
  },
  createRecord(data) {
    return api.post('/borrow-records', data)
  },
  returnBook(id) {
    return api.post(`/borrow-records/${id}/return`)
  },
  getUserRecords(userId) {
    return api.get(`/borrow-records/user/${userId}`)
  },
}
