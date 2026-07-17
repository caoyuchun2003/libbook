import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { commonRoutes } from './common'
import { userRoutes } from './user'
import { adminRoutes } from './admin'

// 合并所有路由
const routes = [
  ...commonRoutes,
  ...userRoutes,
  ...adminRoutes,
]

const router = createRouter({
  history: createWebHistory(
    import.meta.env.VITE_ROUTER_BASE || import.meta.env.BASE_URL || '/'
  ),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // 公开页面，无需认证
  if (to.meta.public) {
    next()
    return
  }

  // 需要管理员权限
  if (to.meta.requiresAdmin) {
    if (!authStore.isAuthenticated) {
      next({ name: 'Login' })
      return
    }
    if (!authStore.isAdmin) {
      next({ name: 'Dashboard' })
      return
    }
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' })
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router
