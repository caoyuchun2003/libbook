<template>
  <div class="books-page">
    <div class="page-header">
      <h2>书城</h2>
      <p class="page-subtitle">挑选一本，慢慢读下去</p>
    </div>

    <div class="search-section">
      <div class="search-bar">
        <el-input
          v-model="searchKeyword"
          placeholder="搜索标题、作者或 ISBN"
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
    </div>

    <div class="category-section">
      <div class="category-header">
        <span class="category-label">分类</span>
        <button
          v-if="selectedCategory"
          type="button"
          class="clear-filter"
          @click="clearCategory"
        >
          清除
        </button>
      </div>
      <div class="category-tags">
        <button
          v-for="cat in categories"
          :key="cat.id"
          type="button"
          class="category-tag"
          :class="{ active: selectedCategory === cat.id }"
          @click="selectCategory(cat.id)"
        >
          {{ cat.name }}
        </button>
      </div>
    </div>

    <div v-loading="loading" class="books-container">
      <el-empty v-if="!loading && books.length === 0" description="没有找到相关图书">
        <el-button type="primary" @click="resetSearch">清除筛选</el-button>
      </el-empty>

      <div v-else class="books-grid">
        <article
          v-for="book in books"
          :key="book.id"
          class="book-card"
          @click="viewBook(book.id)"
        >
          <div class="book-cover-wrapper">
            <BookCover :book="book" class="book-cover" lazy />
          </div>
          <div class="book-info">
            <h3 class="book-title" :title="book.title">{{ book.title }}</h3>
            <p class="book-author">{{ book.author }}</p>
            <div class="book-meta">
              <span class="book-cat">{{ book.category?.name }}</span>
              <span v-if="book.price" class="book-price">¥{{ book.price }}</span>
            </div>
          </div>
        </article>
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
import { Search } from '@element-plus/icons-vue'
import BookCover from '@/components/BookCover.vue'

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
  width: 100%;
}

.page-header {
  margin-bottom: 28px;
}

.page-header h2 {
  margin: 0 0 8px;
  font-size: 36px;
  color: var(--yc-text);
  letter-spacing: 0.06em;
}

.page-subtitle {
  margin: 0;
  font-size: 15px;
  color: var(--yc-muted);
}

.search-section {
  margin-bottom: 20px;
}

.search-bar {
  display: flex;
  max-width: 560px;
}

.search-input {
  width: 100%;
}

.category-section {
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--yc-line);
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.category-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--yc-muted);
  letter-spacing: 0.08em;
}

.clear-filter {
  border: 0;
  background: transparent;
  color: var(--yc-ink-soft);
  font-size: 13px;
  cursor: pointer;
}

.clear-filter:hover {
  color: var(--yc-accent);
}

.category-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.category-tag {
  border: 1px solid var(--yc-line);
  background: var(--yc-surface);
  color: var(--yc-ink-soft);
  padding: 7px 14px;
  font-size: 13px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, border-color 0.2s;
}

.category-tag:hover {
  border-color: var(--yc-ink-soft);
}

.category-tag.active {
  background: var(--yc-ink);
  border-color: var(--yc-ink);
  color: #fff;
}

.books-container {
  min-height: 320px;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(168px, 1fr));
  gap: 28px 20px;
  margin-bottom: 32px;
}

.book-card {
  cursor: pointer;
  background: transparent;
  border: 0;
  padding: 0;
  transition: transform 0.25s ease;
}

.book-card:hover {
  transform: translateY(-4px);
}

.book-cover-wrapper {
  width: 100%;
  padding-top: 140%;
  position: relative;
  margin-bottom: 12px;
  border-radius: 4px;
  overflow: hidden;
  background: var(--yc-surface);
  box-shadow: 0 8px 24px rgba(22, 58, 60, 0.08);
  transition: box-shadow 0.25s ease;
}

.book-card:hover .book-cover-wrapper {
  box-shadow: 0 12px 28px rgba(22, 58, 60, 0.14);
}

.book-cover {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}

.book-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.book-title {
  font-family: var(--yc-font-display);
  font-size: 15px;
  font-weight: 600;
  margin: 0;
  color: var(--yc-text);
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  line-height: 1.35;
  min-height: 2.7em;
}

.book-author {
  font-size: 13px;
  color: var(--yc-muted);
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.book-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
}

.book-cat {
  font-size: 12px;
  color: var(--yc-ink-soft);
}

.book-price {
  font-size: 14px;
  color: var(--yc-accent);
  font-weight: 600;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  padding: 12px 0 8px;
}

@media (max-width: 768px) {
  .page-header h2 {
    font-size: 28px;
  }

  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 20px 14px;
  }
}
</style>
