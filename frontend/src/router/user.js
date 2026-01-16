// 用户端路由 - 需要认证
export const userRoutes = [
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/books',
      },
      {
        path: 'books',
        name: 'Books',
        component: () => import('@/views/Books.vue'),
      },
      {
        path: 'books/:id',
        name: 'BookDetail',
        component: () => import('@/views/BookDetail.vue'),
      },
      {
        path: 'books/:id/read',
        name: 'BookReader',
        component: () => import('@/views/BookReader.vue'),
      },
      {
        path: 'my-bookshelf',
        name: 'MyBookshelf',
        component: () => import('@/views/MyBookshelf.vue'),
      },
    ],
  },
]
