<template>
  <el-container class="main-layout">
    <!-- Header -->
    <el-header class="main-header">
      <div class="header-content">
        <div class="header-left">
          <h1 class="logo" @click="$router.push('/books')">📚 华腾教育</h1>
        </div>
        <div class="header-nav">
          <el-menu
            :default-active="activeNav"
            mode="horizontal"
            router
            class="header-menu"
          >
            <el-menu-item index="/books">
              <el-icon><Reading /></el-icon>
              <span>书城</span>
            </el-menu-item>
            <el-menu-item index="/my-bookshelf">
              <el-icon><Collection /></el-icon>
              <span>书架</span>
            </el-menu-item>
            <el-menu-item index="/my-bookshelf">
              <el-icon><Collection /></el-icon>
              <span>我的</span>
            </el-menu-item>
          </el-menu>
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

    <!-- Main Content -->
    <el-main class="main-content">
      <router-view />
    </el-main>

    <!-- Footer -->
    <el-footer class="main-footer">
      <div class="footer-content">
        <div class="footer-main">
          <div class="footer-left">
            <h3 class="footer-title">📚 华腾教育</h3>
            <p class="footer-desc">专业的图书管理系统</p>
          </div>
          <div class="footer-links">
            <div class="link-group">
              <h4>快速链接</h4>
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
          <p>© 2024 华腾教育 - 图书管理系统 | All Rights Reserved</p>
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
  if (route.path.startsWith('/books')) return '/books'
  if (route.path.startsWith('/my-bookshelf')) return '/my-bookshelf'
  return '/books'
})

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

/* Header Styles */
.main-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  position: sticky;
  top: 0;
  z-index: 1000;
  height: 80px !important;
  backdrop-filter: blur(10px);
}

.header-content {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 30px;
  height: 100%;
  gap: 20px;
}

.header-left {
  flex-shrink: 0;
  min-width: 180px;
}

.logo {
  margin: 0;
  font-size: 32px;
  font-weight: 800;
  color: white;
  letter-spacing: 1.5px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: transform 0.3s, opacity 0.3s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.logo:hover {
  transform: scale(1.05);
  opacity: 0.9;
}

.header-nav {
  flex: 1;
  display: flex;
  justify-content: center;
  min-width: 0;
}

.header-nav :deep(.el-menu) {
  display: flex;
  flex-wrap: nowrap;
}

.header-menu {
  background: transparent !important;
  border: none !important;
  color: white;
}

/* 移除 el-menu 的所有默认样式 */
:deep(.header-menu) {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
}

:deep(.header-menu::before),
:deep(.header-menu::after) {
  display: none !important;
}

/* 移除菜单项的所有默认边框和装饰 */
:deep(.header-menu .el-menu-item) {
  border: none !important;
  border-bottom: none !important;
  border-top: none !important;
  border-left: none !important;
  border-right: none !important;
}

:deep(.header-menu .el-menu-item::before),
:deep(.header-menu .el-menu-item::after) {
  display: none !important;
  content: none !important;
}

:deep(.header-menu .el-menu-item) {
  color: white !important;
  font-size: 20px !important;
  font-weight: 500 !important;
  height: 80px;
  line-height: 80px;
  padding: 0 40px !important;
  border: none !important;
  border-bottom: none !important;
  transition: all 0.2s;
  text-shadow: none;
  white-space: nowrap;
  min-width: auto;
  border-radius: 0 !important;
  margin: 0 !important;
  background: transparent !important;
}

:deep(.header-menu .el-menu-item::before),
:deep(.header-menu .el-menu-item::after) {
  display: none !important;
}

:deep(.header-menu .el-menu-item .el-icon) {
  font-size: 20px !important;
  margin-right: 8px;
  vertical-align: middle;
}

:deep(.header-menu .el-menu-item span) {
  font-size: 20px !important;
  font-weight: 500 !important;
  letter-spacing: 0;
  text-shadow: none;
  display: inline-block;
  white-space: nowrap;
}

:deep(.header-menu .el-menu-item:hover) {
  color: white !important;
  background: rgba(255, 255, 255, 0.1) !important;
  border-bottom: none;
  transform: none;
}

:deep(.header-menu .el-menu-item.is-active) {
  color: white !important;
  background: rgba(255, 255, 255, 0.15) !important;
  border-bottom: none;
  font-weight: 600 !important;
  text-shadow: none;
  box-shadow: none;
}

.header-right {
  flex-shrink: 0;
  min-width: 150px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 10px 18px;
  border-radius: 8px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  color: white;
  font-size: 16px;
  font-weight: 500;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.user-info .el-icon {
  font-size: 18px;
  transition: transform 0.3s;
}

.user-info:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.user-info:hover .el-icon:last-child {
  transform: rotate(180deg);
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 20px;
  background-color: #f5f7fa;
  min-height: calc(100vh - 160px);
  max-width: 1400px;
  width: 100%;
  margin: 0 auto;
}

/* Footer Styles */
.main-footer {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
  color: #ecf0f1;
  padding: 40px 20px 30px !important;
  margin-top: auto;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
  min-height: 200px !important;
  width: 100% !important;
  height: auto !important;
}

:deep(.el-footer) {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
  padding: 0 !important;
  height: auto !important;
  min-height: 200px !important;
}

:deep(.el-footer.main-footer) {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
}

.footer-content {
  max-width: 1400px;
  margin: 0 auto;
}

.footer-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  padding-bottom: 30px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-left {
  flex: 1;
}

.footer-title {
  margin: 0 0 10px 0;
  font-size: 24px;
  font-weight: 700;
  color: #fff;
  letter-spacing: 1px;
}

.footer-desc {
  margin: 0;
  font-size: 14px;
  color: #bdc3c7;
  line-height: 1.6;
}

.footer-links {
  display: flex;
  gap: 60px;
}

.link-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.link-group h4 {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
}

.link-group .link {
  color: #bdc3c7;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
  padding: 4px 0;
  display: inline-block;
}

.link-group .link:hover {
  color: #409eff;
  transform: translateX(4px);
}

.footer-bottom {
  text-align: center;
  padding-top: 20px;
  padding-bottom: 10px;
}

.footer-bottom p {
  margin: 0;
  font-size: 13px;
  color: #95a5a6;
  line-height: 1.8;
}

/* 响应式 */
@media (max-width: 768px) {
  .main-footer {
    padding: 30px 15px 15px;
  }

  .footer-main {
    flex-direction: column;
    gap: 30px;
    margin-bottom: 20px;
    padding-bottom: 20px;
  }

  .footer-links {
    flex-direction: column;
    gap: 30px;
    width: 100%;
  }

  .footer-title {
    font-size: 20px;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .main-header {
    height: auto !important;
  }

  .header-content {
    flex-wrap: wrap;
    padding: 15px;
  }

  .logo {
    font-size: 24px;
  }

  .header-nav {
    order: 3;
    width: 100%;
    margin-top: 15px;
  }

  .header-menu {
    width: 100%;
  }

  :deep(.header-menu .el-menu-item) {
    height: 60px;
    line-height: 60px;
    font-size: 20px !important;
    padding: 0 25px !important;
  }

  :deep(.header-menu .el-menu-item span) {
    font-size: 20px !important;
    font-weight: 700 !important;
  }

  :deep(.header-menu .el-menu-item .el-icon) {
    font-size: 22px !important;
  }

  .user-info {
    font-size: 14px;
    padding: 8px 12px;
  }

  .main-content {
    padding: 15px;
    min-height: calc(100vh - 180px);
  }
}
</style>
