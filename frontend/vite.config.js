import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  // 默认 /libbook/ 兼容百度云同机；Pages 自定义域构建时设 VITE_BASE=/
  const base = env.VITE_BASE || process.env.VITE_BASE || '/libbook/'

  return {
    base,
    plugins: [vue()],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url)),
      },
    },
    server: {
      host: '0.0.0.0',
      port: 5173,
      proxy: {
        '/api': {
          target: 'http://localhost:8000',
          changeOrigin: true,
          configure: (proxy) => {
            proxy.on('error', (err) => {
              console.log('代理错误:', err.message)
            })
          },
        },
        '/libbook/api': {
          target: 'http://localhost:8000',
          changeOrigin: true,
          rewrite: (p) => p.replace(/^\/libbook\/api/, '/api'),
        },
      },
    },
  }
})
