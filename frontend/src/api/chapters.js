import api from './axios'

export const chaptersApi = {
  // 获取图书的所有章节
  getChapters(bookId) {
    return api.get(`/books/${bookId}/chapters`)
  },

  // 创建章节
  createChapter(bookId, data) {
    return api.post(`/books/${bookId}/chapters`, data)
  },

  // 更新章节
  updateChapter(chapterId, data) {
    return api.put(`/chapters/${chapterId}`, data)
  },

  // 删除章节
  deleteChapter(chapterId) {
    return api.delete(`/chapters/${chapterId}`)
  },

  // 更新章节顺序
  updateChapterOrder(bookId, chapters) {
    return api.put(`/books/${bookId}/chapters/order`, { chapters })
  },
}
