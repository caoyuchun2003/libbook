<template>
  <div class="dashboard">
    <h2>欢迎使用 华腾教育 图书管理系统</h2>
    <el-row :gutter="20" style="margin-top: 20px">
      <el-col :span="8">
        <el-card>
          <div class="stat-item">
            <div class="stat-value">{{ stats.totalBooks }}</div>
            <div class="stat-label">图书总数</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card>
          <div class="stat-item">
            <div class="stat-value">{{ stats.availableBooks }}</div>
            <div class="stat-label">可借图书</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card>
          <div class="stat-item">
            <div class="stat-value">{{ stats.categories }}</div>
            <div class="stat-label">图书分类</div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { booksApi } from '@/api/books'
import { categoriesApi } from '@/api/categories'

const stats = ref({
  totalBooks: 0,
  availableBooks: 0,
  categories: 0,
})

onMounted(async () => {
  try {
    const [booksRes, categoriesRes] = await Promise.all([
      booksApi.getBooks(),
      categoriesApi.getCategories(),
    ])
    
    stats.value.totalBooks = booksRes.data.total || 0
    stats.value.availableBooks = booksRes.data.data?.reduce((sum, book) => sum + book.available_copies, 0) || 0
    stats.value.categories = categoriesRes.data.length || 0
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
})
</script>

<style scoped>
.dashboard {
  padding: 20px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 32px;
  font-weight: bold;
  color: #409eff;
}

.stat-label {
  margin-top: 10px;
  color: #666;
}
</style>
