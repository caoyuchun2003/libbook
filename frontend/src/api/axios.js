import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// 请求拦截器 - 添加 token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// 响应拦截器 - 处理错误
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // 处理连接错误（后端未启动）
    if (error.code === 'ECONNREFUSED' || error.message?.includes('Network Error')) {
      console.error('无法连接到后端服务器，请确保后端服务正在运行 (http://localhost:8000)')
      return Promise.reject({
        ...error,
        message: '无法连接到服务器，请检查后端服务是否已启动',
        isConnectionError: true
      })
    }
    
    if (error.response?.status === 401) {
      // 文档查看器是公开的，401 错误不应该跳转到登录页
      const isPublicRoute = window.location.pathname.startsWith('/docs')
      if (!isPublicRoute) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api
