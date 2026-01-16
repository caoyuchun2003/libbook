// 管理端路由 - 需要管理员权限
export const adminRoutes = [
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: () => import('@/views/admin/AdminDashboard.vue'),
      },
      {
        path: 'books',
        name: 'AdminBooks',
        component: () => import('@/views/admin/AdminBooks.vue'),
      },
      {
        path: 'books/new',
        name: 'BookCreate',
        component: () => import('@/views/admin/BookEdit.vue'),
      },
      {
        path: 'books/:id',
        name: 'BookEdit',
        component: () => import('@/views/admin/BookEdit.vue'),
      },
      {
        path: 'books/:bookId/chapters/:chapterId/edit',
        name: 'ChapterEdit',
        component: () => import('@/views/admin/ChapterEdit.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'categories',
        name: 'CategoryManagement',
        component: () => import('@/views/admin/CategoryManagement.vue'),
      },
      {
        path: 'users',
        name: 'UserManagement',
        component: () => import('@/views/admin/UserManagement.vue'),
      },
      {
        path: 'roles',
        name: 'RoleManagement',
        component: () => import('@/views/admin/RoleManagement.vue'),
      },
      {
        path: 'statistics',
        name: 'Statistics',
        component: () => import('@/views/admin/Statistics.vue'),
      },
    ],
  },
]
