<template>
  <div class="books-page">
    <div class="page-header">
      <h2>书城</h2>
      <p class="page-subtitle">发现你喜欢的图书</p>
    </div>

    <!-- 搜索栏 -->
    <div class="search-section">
      <el-card shadow="never" class="search-card">
        <div class="search-bar">
          <el-input
            v-model="searchKeyword"
            placeholder="搜索图书（标题、作者、ISBN）"
            size="large"
            clearable
            @keyup.enter="loadBooks"
            @clear="loadBooks"
            class="search-input"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
            <template #append>
              <el-button type="primary" @click="loadBooks">搜索</el-button>
            </template>
          </el-input>
        </div>
      </el-card>
    </div>

    <!-- 分类筛选 -->
    <div class="category-section">
      <div class="category-header">
        <span class="category-label">分类筛选：</span>
        <el-button 
          v-if="selectedCategory" 
          text 
          type="primary" 
          size="small"
          @click="clearCategory"
        >
          清除筛选
        </el-button>
      </div>
      <div class="category-tags">
        <el-tag
          v-for="cat in categories"
          :key="cat.id"
          :type="selectedCategory === cat.id ? 'primary' : 'info'"
          :effect="selectedCategory === cat.id ? 'dark' : 'plain'"
          class="category-tag"
          @click="selectCategory(cat.id)"
        >
          {{ cat.name }}
        </el-tag>
      </div>
    </div>

    <!-- 图书网格 -->
    <div v-loading="loading" class="books-container">
      <el-empty v-if="!loading && books.length === 0" description="暂无图书">
        <el-button type="primary" @click="resetSearch">清除筛选</el-button>
      </el-empty>
      
      <div v-else class="books-grid">
        <el-card
          v-for="book in books"
          :key="book.id"
          class="book-card"
          shadow="hover"
          @click="viewBook(book.id)"
        >
          <div class="book-cover-wrapper">
            <el-image
              :src="book.cover"
              fit="cover"
              class="book-cover"
              :preview-src-list="[book.cover]"
              lazy
            >
              <template #error>
                <div class="cover-error">
                  <el-icon><Picture /></el-icon>
                  <span>加载失败</span>
                </div>
              </template>
            </el-image>
          </div>
          <div class="book-info">
            <h3 class="book-title" :title="book.title">{{ book.title }}</h3>
            <p class="book-author">{{ book.author }}</p>
            <div class="book-meta">
              <el-tag size="small" type="info">{{ book.category?.name }}</el-tag>
              <span v-if="book.price" class="book-price">¥{{ book.price }}</span>
            </div>
            <div class="book-footer">
              <el-button type="primary" size="small" @click.stop="viewBook(book.id)">
                查看详情
              </el-button>
            </div>
          </div>
        </el-card>
      </div>
    </div>

    <!-- 分页 -->
    <div class="pagination-wrapper" v-if="total > 0">
      <el-pagination
        v-model:current-page="currentPage"
        v-model:page-size="pageSize"
        :total="total"
        :page-sizes="[12, 24, 48, 96]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { booksApi } from '@/api/books'
import { categoriesApi } from '@/api/categories'
import { ElMessage } from 'element-plus'
import { Search, Picture } from '@element-plus/icons-vue'

const router = useRouter()

const loading = ref(false)
const books = ref([])
const categories = ref([])
const searchKeyword = ref('')
const selectedCategory = ref(null)
const currentPage = ref(1)
const pageSize = ref(24)
const total = ref(0)

onMounted(() => {
  loadCategories()
  loadBooks()
})

const loadCategories = async () => {
  try {
    const res = await categoriesApi.getCategories()
    categories.value = res.data
  } catch (error) {
    ElMessage.error('加载分类失败')
  }
}

const loadBooks = async () => {
  loading.value = true
  try {
    currentPage.value = 1 // 搜索时重置到第一页
    const params = {
      page: currentPage.value,
      per_page: pageSize.value,
    }
    if (searchKeyword.value) {
      params.search = searchKeyword.value
    }
    if (selectedCategory.value) {
      params.category_id = selectedCategory.value
    }
    const res = await booksApi.getBooks(params)
    books.value = res.data.data || []
    total.value = res.data.total || 0
  } catch (error) {
    ElMessage.error('加载图书失败')
  } finally {
    loading.value = false
  }
}

const handleSizeChange = (val) => {
  pageSize.value = val
  currentPage.value = 1
  loadBooks()
}

const handleCurrentChange = (val) => {
  currentPage.value = val
  loadBooks()
}

const resetSearch = () => {
  searchKeyword.value = ''
  selectedCategory.value = null
  loadBooks()
}

const selectCategory = (categoryId) => {
  if (selectedCategory.value === categoryId) {
    selectedCategory.value = null
  } else {
    selectedCategory.value = categoryId
  }
  loadBooks()
}

const clearCategory = () => {
  selectedCategory.value = null
  loadBooks()
}

const viewBook = (id) => {
  router.push(`/books/${id}`)
}
</script>

<style scoped>
.books-page {
  padding: 0;
  width: 100%;
}

.page-header {
  margin-bottom: 30px;
  text-align: center;
}

.page-header h2 {
  margin: 0 0 10px 0;
  font-size: 42px;
  font-weight: 700;
  color: #303133;
  letter-spacing: 2px;
}

.page-subtitle {
  margin: 0;
  font-size: 16px;
  color: #909399;
  font-weight: 400;
}

.search-section {
  margin-bottom: 20px;
}

.search-card {
  border-radius: 12px;
  border: 1px solid #e4e7ed;
}

.search-bar {
  display: flex;
  justify-content: center;
}

.search-input {
  max-width: 600px;
  width: 100%;
}

.search-input :deep(.el-input__inner) {
  height: 50px;
  font-size: 16px;
  border-radius: 25px;
}

.search-input :deep(.el-input__wrapper) {
  border-radius: 25px;
  padding-left: 20px;
}

.category-section {
  margin-bottom: 30px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 12px;
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.category-label {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.category-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.category-tag {
  cursor: pointer;
  padding: 10px 20px;
  font-size: 15px;
  font-weight: 500;
  border-radius: 20px;
  transition: all 0.3s;
  user-select: none;
}

.category-tag:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.category-tag.is-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: transparent;
  color: white;
}

.books-container {
  min-height: 400px;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 24px;
  margin-bottom: 40px;
}

.book-card {
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 12px;
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 16px;
}

.book-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
}

.book-cover-wrapper {
  width: 100%;
  padding-top: 130%;
  position: relative;
  margin-bottom: 12px;
  border-radius: 8px;
  overflow: hidden;
  background: #f5f7fa;
}

.book-cover {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

.cover-error {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #909399;
  background: #f5f7fa;
}

.cover-error .el-icon {
  font-size: 32px;
  margin-bottom: 8px;
}


.book-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 0;
  gap: 8px;
}

.book-title {
  font-size: 15px;
  font-weight: 600;
  margin: 0;
  color: #303133;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  line-height: 1.3;
  min-height: 39px;
}

.book-author {
  font-size: 12px;
  color: #909399;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.book-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 0;
  min-height: 28px;
}

.book-meta :deep(.el-tag) {
  font-size: 12px;
  padding: 4px 10px;
}

.book-price {
  font-size: 16px;
  color: #f56c6c;
  font-weight: 700;
}

.book-footer {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: auto;
  padding-top: 10px;
  border-top: 1px solid #f0f0f0;
}

.book-footer :deep(.el-button) {
  padding: 6px 12px;
  font-size: 12px;
  width: 100%;
}


.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 40px;
  padding: 20px 0;
}

:deep(.el-pagination) {
  font-size: 16px;
}

:deep(.el-pagination .el-pager li) {
  font-size: 16px;
  min-width: 40px;
  height: 40px;
  line-height: 40px;
}

:deep(.el-pagination .btn-prev),
:deep(.el-pagination .btn-next) {
  font-size: 16px;
  min-width: 40px;
  height: 40px;
  line-height: 40px;
}

/* 响应式设计 */
@media (max-width: 1400px) {
  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 22px;
  }
}

@media (max-width: 1200px) {
  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px;
  }
}

@media (max-width: 768px) {
  .page-header h2 {
    font-size: 32px;
  }

  .search-bar {
    flex-direction: column;
  }

  .search-bar :deep(.el-input) {
    max-width: 100%;
  }

  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
  }

  .book-card {
    padding: 12px;
  }

  .book-title {
    font-size: 14px;
    min-height: 40px;
  }

  .book-price {
    font-size: 16px;
  }
}
</style>
