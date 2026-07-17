import axios from 'axios'

function resolveApiBase() {
  const configured = import.meta.env.VITE_API_BASE?.replace(/\/$/, '')
  if (configured) return configured
  // 与 Vite base 对齐：/libbook/ → /libbook/api ；/ → /api
  const base = import.meta.env.BASE_URL || '/'
  return `${base}api`.replace(/\/+/g, '/')
}

const api = axios.create({
  baseURL: resolveApiBase(),
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
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
    if (error.code === 'ECONNREFUSED' || error.message?.includes('Network Error')) {
      console.error('无法连接到后端服务器')
      return Promise.reject({
        ...error,
        message: '无法连接到服务器，请检查后端服务是否已启动',
        isConnectionError: true,
      })
    }

    if (error.response?.status === 401) {
      const base = import.meta.env.BASE_URL || '/'
      const isPublicRoute = window.location.pathname.startsWith(
        `${base}docs`.replace(/\/+/g, '/')
      )
      if (!isPublicRoute) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        window.location.href = `${base}login`.replace(/\/+/g, '/')
      }
    }
    return Promise.reject(error)
  }
)

export default api
