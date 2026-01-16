// 公共路由 - 无需认证
export const commonRoutes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { requiresGuest: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/Register.vue'),
    meta: { requiresGuest: true },
  },
  // 文档查看器 - 公开访问，无需登录
  {
    path: '/docs',
    name: 'Docs',
    component: () => import('@/views/DocsViewer.vue'),
    meta: { public: true },
  },
  {
    path: '/docs/:filename',
    name: 'DocViewer',
    component: () => import('@/views/DocsViewer.vue'),
    meta: { public: true },
  },
]
