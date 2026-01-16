<template>
  <div class="admin-dashboard">
    <h2>管理首页</h2>
    
    <el-row :gutter="20" style="margin-top: 20px" justify="center">
      <el-col :span="8">
        <el-card class="stat-card">
          <div class="stat-item">
            <div class="stat-icon" style="background: #409eff;">
              <el-icon size="30"><Reading /></el-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ stats.totalBooks }}</div>
              <div class="stat-label">图书总数</div>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card class="stat-card">
          <div class="stat-item">
            <div class="stat-icon" style="background: #67c23a;">
              <el-icon size="30"><User /></el-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ stats.totalUsers }}</div>
              <div class="stat-label">用户总数</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="20" style="margin-top: 20px">
      <el-col :span="24">
        <el-card>
          <template #header>
            <span>快速操作</span>
          </template>
          <div class="quick-actions">
            <el-button type="primary" @click="$router.push('/admin/books')" size="large">
              <el-icon><Plus /></el-icon>
              添加图书
            </el-button>
            <el-button type="success" @click="$router.push('/admin/categories')" size="large">
              <el-icon><FolderAdd /></el-icon>
              管理分类
            </el-button>
            <el-button type="info" @click="$router.push('/admin/users')" size="large">
              <el-icon><UserFilled /></el-icon>
              用户管理
            </el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { booksApi } from '@/api/books'
import { Reading, User, Plus, FolderAdd, UserFilled } from '@element-plus/icons-vue'

const stats = ref({
  totalBooks: 0,
  totalUsers: 0,
})

onMounted(() => {
  loadStats()
})

const loadStats = async () => {
  try {
    const booksRes = await booksApi.getBooks({ per_page: 1 })
    stats.value.totalBooks = booksRes.data.total || 0
    
    // TODO: 获取用户总数（需要后端 API）
    stats.value.totalUsers = 0
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
}
</script>

<style scoped>
.admin-dashboard {
  padding: 20px;
}

.stat-card {
  margin-bottom: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 15px;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 28px;
  font-weight: bold;
  color: #303133;
}

.stat-label {
  margin-top: 5px;
  color: #909399;
  font-size: 14px;
}

.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.quick-actions .el-button {
  width: 100%;
  justify-content: flex-start;
}
</style>
