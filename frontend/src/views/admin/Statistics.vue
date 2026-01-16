<template>
  <div class="statistics">
    <h2>数据统计</h2>

    <el-row :gutter="20" style="margin-top: 20px">
      <el-col :span="12">
        <el-card>
          <template #header>
            <span>图书统计</span>
          </template>
          <div class="chart-placeholder">
            <p>图书总数：{{ stats.totalBooks }}</p>
            <p>可借图书：{{ stats.availableBooks }}</p>
            <p>已借出：{{ stats.totalBooks - stats.availableBooks }}</p>
            <p>分类数量：{{ stats.totalCategories }}</p>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="20" style="margin-top: 20px">
      <el-col :span="12">
        <el-card>
          <template #header>
            <span>用户统计</span>
          </template>
          <div class="chart-placeholder">
            <p>总用户数：{{ stats.totalUsers }}</p>
            <p>管理员：{{ stats.adminUsers }}</p>
            <p>普通用户：{{ stats.totalUsers - stats.adminUsers }}</p>
          </div>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card>
          <template #header>
            <span>热门图书</span>
          </template>
          <el-table :data="popularBooks" style="width: 100%">
            <el-table-column prop="title" label="图书" />
            <el-table-column prop="borrow_count" label="借阅次数" />
          </el-table>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { booksApi } from '@/api/books'
import { categoriesApi } from '@/api/categories'
import { ElMessage } from 'element-plus'

const stats = ref({
  totalBooks: 0,
  availableBooks: 0,
  totalCategories: 0,
  totalUsers: 0,
  adminUsers: 0,
})

const popularBooks = ref([])

onMounted(() => {
  loadStatistics()
})

const loadStatistics = async () => {
  try {
    const [booksRes, categoriesRes] = await Promise.all([
      booksApi.getBooks({ per_page: 1 }),
      categoriesApi.getCategories(),
    ])
    
    stats.value.totalBooks = booksRes.data.total || 0
    stats.value.totalCategories = categoriesRes.data.length || 0
    
    // 计算可借图书
    const books = booksRes.data.data || []
    stats.value.availableBooks = books.reduce((sum, book) => sum + (book.available_copies || 0), 0)
    
    // 模拟用户统计（需要后端 API）
    stats.value.totalUsers = 2
    stats.value.adminUsers = 1
    
    // 模拟热门图书（需要后端 API）
    popularBooks.value = [
      { title: 'Vue.js 实战', borrow_count: 15 },
      { title: 'Laravel 框架开发', borrow_count: 12 },
      { title: '红楼梦', borrow_count: 8 },
    ]
  } catch (error) {
    ElMessage.error('加载统计数据失败')
    console.error(error)
  }
}
</script>

<style scoped>
.statistics {
  padding: 20px;
}

.chart-placeholder {
  padding: 20px;
}

.chart-placeholder p {
  margin: 10px 0;
  font-size: 16px;
}
</style>
