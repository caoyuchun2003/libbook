<template>
  <el-container class="main-layout">
    <el-header class="main-header">
      <div class="header-content">
        <div class="header-left">
          <button type="button" class="logo" @click="$router.push('/books')">
            <svg class="logo-mark" viewBox="0 0 64 64" aria-hidden="true">
              <rect width="64" height="64" rx="12" fill="currentColor" opacity="0.18"/>
              <path d="M18 16h20c4 0 7 3 7 7v25H25c-4 0-7-3-7-7V16z" fill="#f7faf9" opacity=".92"/>
              <path d="M18 16v25c0 4 3 7 7 7h20" stroke="#c4783a" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span class="logo-text">宇春书城</span>
          </button>
        </div>
        <div class="header-nav">
          <nav class="nav-links" aria-label="主导航">
            <router-link
              to="/books"
              class="nav-link"
              :class="{ active: activeNav === '/books' }"
            >
              <el-icon><Reading /></el-icon>
              <span>书城</span>
            </router-link>
            <router-link
              to="/my-bookshelf"
              class="nav-link"
              :class="{ active: activeNav === '/my-bookshelf' }"
            >
              <el-icon><Collection /></el-icon>
              <span>书架</span>
            </router-link>
          </nav>
        </div>
        <div class="header-right">
          <el-dropdown @command="handleCommand">
            <span class="user-info">
              <el-icon><User /></el-icon>
              <span>{{ authStore.user?.name }}</span>
              <el-icon><ArrowDown /></el-icon>
            </span>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item v-if="authStore.isAdmin" command="admin">
                  <el-icon><Setting /></el-icon>
                  后台管理
                </el-dropdown-item>
                <el-dropdown-item command="logout">
                  <el-icon><SwitchButton /></el-icon>
                  退出登录
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </div>
    </el-header>

    <el-main class="main-content" :class="{ 'is-reader': isReader }">
      <router-view />
    </el-main>

    <el-footer v-if="!isReader" class="main-footer">
      <div class="footer-content">
        <div class="footer-main">
          <div class="footer-left">
            <h3 class="footer-title">宇春书城</h3>
            <p class="footer-desc">读好书，管好书</p>
          </div>
          <div class="footer-links">
            <div class="link-group">
              <h4>浏览</h4>
              <span @click="$router.push('/books')" class="link">书城</span>
              <span @click="$router.push('/my-bookshelf')" class="link">我的书架</span>
            </div>
            <div class="link-group">
              <h4>帮助</h4>
              <span @click="$router.push('/docs')" class="link">项目文档</span>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <p>© 2026 宇春书城</p>
        </div>
      </div>
    </el-footer>
  </el-container>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { ElMessage } from 'element-plus'
import { Reading, Collection, User, ArrowDown, Setting, SwitchButton } from '@element-plus/icons-vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const activeNav = computed(() => {
  if (route.path.startsWith('/my-bookshelf')) return '/my-bookshelf'
  if (route.path.startsWith('/books')) return '/books'
  return '/books'
})

const isReader = computed(() => route.name === 'BookReader')

const handleCommand = async (command) => {
  if (command === 'logout') {
    await authStore.logout()
    ElMessage.success('已退出登录')
    router.push('/login')
  } else if (command === 'admin') {
    router.push('/admin')
  }
}
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-header {
  background:
    linear-gradient(160deg, rgba(255, 255, 255, 0.06), transparent 40%),
    var(--yc-ink);
  color: white;
  padding: 0;
  position: sticky;
  top: 0;
  z-index: 1000;
  height: 72px !important;
  animation: headerIn 0.45s ease-out;
}

@keyframes headerIn {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.header-content {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 24px;
  height: 100%;
  gap: 16px;
}

.header-left {
  flex-shrink: 0;
}

.logo {
  margin: 0;
  padding: 0;
  border: 0;
  background: transparent;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: opacity 0.25s, transform 0.25s;
}

.logo:hover {
  opacity: 0.92;
  transform: translateY(-1px);
}

.logo-mark {
  width: 36px;
  height: 36px;
  color: #fff;
}

.logo-text {
  font-family: var(--yc-font-display);
  font-size: 24px;
  font-weight: 700;
  letter-spacing: 0.08em;
}

.header-nav {
  flex: 1;
  display: flex;
  justify-content: center;
  min-width: 0;
}

.nav-links {
  display: flex;
  align-items: stretch;
  gap: 4px;
  height: 72px;
}

.nav-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 0 20px;
  color: rgba(255, 255, 255, 0.82);
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  border-bottom: 2px solid transparent;
  transition: color 0.2s, background 0.2s, border-color 0.2s;
}

.nav-link:hover {
  color: #fff;
  background: rgba(255, 255, 255, 0.08);
}

.nav-link.active {
  color: #fff;
  border-bottom-color: var(--yc-accent);
  background: rgba(255, 255, 255, 0.1);
}

.header-right {
  flex-shrink: 0;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 8px 14px;
  border-radius: 8px;
  color: white;
  font-size: 14px;
  font-weight: 500;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.16);
  transition: background 0.2s, border-color 0.2s;
}

.user-info:hover {
  background: rgba(255, 255, 255, 0.14);
  border-color: rgba(255, 255, 255, 0.28);
}

.main-content {
  flex: 1;
  padding: 28px 24px 40px;
  background:
    radial-gradient(ellipse at top, rgba(22, 58, 60, 0.04), transparent 55%),
    var(--yc-paper);
  min-height: calc(100vh - 72px - 180px);
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
}

.main-content.is-reader {
  max-width: none;
  padding: 0;
  min-height: calc(100vh - 72px);
  background: var(--yc-paper);
}

.main-footer {
  background: var(--yc-ink) !important;
  color: rgba(247, 250, 249, 0.78);
  padding: 36px 24px 24px !important;
  margin-top: auto;
  height: auto !important;
  min-height: auto !important;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  gap: 32px;
}

.footer-title {
  margin: 0 0 8px;
  font-size: 20px;
  color: #fff;
  font-family: var(--yc-font-display);
}

.footer-desc {
  margin: 0;
  font-size: 14px;
  color: rgba(247, 250, 249, 0.55);
}

.footer-links {
  display: flex;
  gap: 48px;
}

.link-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.link-group h4 {
  margin: 0 0 4px;
  font-size: 13px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
  letter-spacing: 0.04em;
}

.link-group .link {
  color: rgba(247, 250, 249, 0.55);
  cursor: pointer;
  transition: color 0.2s;
  font-size: 14px;
}

.link-group .link:hover {
  color: var(--yc-accent);
}

.footer-bottom {
  text-align: center;
}

.footer-bottom p {
  margin: 0;
  font-size: 13px;
  color: rgba(247, 250, 249, 0.4);
}

@media (max-width: 768px) {
  .main-header {
    height: auto !important;
  }

  .header-content {
    flex-wrap: wrap;
    padding: 12px 16px;
  }

  .logo-text {
    font-size: 20px;
  }

  .header-nav {
    order: 3;
    width: 100%;
  }

  .nav-links {
    width: 100%;
    height: 48px;
  }

  .nav-link {
    flex: 1;
    justify-content: center;
    padding: 0 12px;
  }

  .main-content {
    padding: 20px 16px 32px;
  }

  .footer-main {
    flex-direction: column;
  }

  .footer-links {
    gap: 24px;
  }
}
</style>
