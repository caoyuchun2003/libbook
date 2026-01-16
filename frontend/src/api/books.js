import api from './axios'

export const booksApi = {
  getBooks(params) {
    return api.get('/books', { params })
  },
  getBook(id) {
    return api.get(`/books/${id}`)
  },
  createBook(data) {
    return api.post('/books', data)
  },
  updateBook(id, data) {
    return api.put(`/books/${id}`, data)
  },
  deleteBook(id) {
    return api.delete(`/books/${id}`)
  },
  searchBooks(keyword) {
    return api.get(`/books/search/${keyword}`)
  },
}
