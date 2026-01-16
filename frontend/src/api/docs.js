import api from './axios'

export const docsApi = {
  listDocs() {
    return api.get('/docs')
  },
  getDoc(filename) {
    return api.get(`/docs/${filename}`)
  },
}
