<template>
  <el-container>
    <el-header class="admin-header">
      <div class="header-content">
        <div class="header-left">
          <h2>📚 华腾教育 后台管理</h2>
        </div>
        <div class="header-right">
          <span>管理员：{{ authStore.user?.name }}</span>
          <el-button @click="goToUserView">返回用户端</el-button>
          <el-button @click="handleLogout">退出</el-button>
        </div>
      </div>
    </el-header>
    <el-container>
      <el-aside width="250px" class="admin-sidebar">
        <el-menu
          :default-active="activeMenu"
          router
          class="admin-menu"
        >
          <el-menu-item index="/admin">
            <el-icon><DataBoard /></el-icon>
            <span>管理首页</span>
          </el-menu-item>
          <el-menu-item index="/admin/books">
            <el-icon><Reading /></el-icon>
            <span>图书管理</span>
          </el-menu-item>
          <el-menu-item index="/admin/categories">
            <el-icon><FolderOpened /></el-icon>
            <span>分类管理</span>
          </el-menu-item>
          <el-menu-item index="/admin/users">
            <el-icon><User /></el-icon>
            <span>用户管理</span>
          </el-menu-item>
          <el-menu-item index="/admin/roles">
            <el-icon><Setting /></el-icon>
            <span>角色管理</span>
          </el-menu-item>
          <el-menu-item index="/admin/statistics">
            <el-icon><DataAnalysis /></el-icon>
            <span>数据统计</span>
          </el-menu-item>
        </el-menu>
      </el-aside>
      <el-main class="admin-content">
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { ElMessage } from 'element-plus'
import { DataBoard, Reading, FolderOpened, User, Setting, DataAnalysis } from '@element-plus/icons-vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const activeMenu = computed(() => route.path)

const handleLogout = async () => {
  await authStore.logout()
  ElMessage.success('已退出登录')
  router.push('/login')
}

const goToUserView = () => {
  router.push('/')
}
</script>

<style scoped>
.admin-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0 20px;
}

.header-content {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 500;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

.admin-sidebar {
  background-color: #f5f5f5;
  border-right: 1px solid #e4e7ed;
}

.admin-menu {
  border: none;
  background-color: transparent;
}

.admin-content {
  padding: 20px;
  background-color: #fafafa;
}
</style>
