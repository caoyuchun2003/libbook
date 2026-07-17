<template>
  <div class="my-bookshelf" v-loading="loading">
    <div class="page-header">
      <h2>我的书架</h2>
      <p class="page-subtitle">收藏与常读的书都在这里</p>
    </div>

    <el-empty
      v-if="!loading && books.length === 0"
      description="书架还是空的，去书城挑一本吧"
      :image-size="140"
    >
      <el-button type="primary" @click="$router.push('/books')">去书城</el-button>
    </el-empty>

    <div v-else class="books-grid">
      <article
        v-for="book in books"
        :key="book.id"
        class="book-card"
        @click="viewBook(book.id)"
      >
        <div class="book-cover-wrapper">
          <BookCover :book="book" class="book-cover" />
        </div>
        <div class="book-info">
          <h3 class="book-title">{{ book.title }}</h3>
          <p class="book-author">{{ book.author }}</p>
          <div class="book-meta">
            <span class="book-cat">{{ book.category?.name }}</span>
            <span v-if="book.price" class="book-price">¥{{ book.price }}</span>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { booksApi } from '@/api/books'
import { ElMessage } from 'element-plus'
import BookCover from '@/components/BookCover.vue'

const router = useRouter()
const books = ref([])
const loading = ref(false)

onMounted(() => {
  loadBookshelf()
})

const loadBookshelf = async () => {
  loading.value = true
  try {
    const res = await booksApi.getBooks({ per_page: 20 })
    books.value = res.data.data || []
  } catch (error) {
    ElMessage.error('加载书架失败')
  } finally {
    loading.value = false
  }
}

const viewBook = (id) => {
  router.push(`/books/${id}`)
}
</script>

<style scoped>
.my-bookshelf {
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

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(168px, 1fr));
  gap: 28px 20px;
}

.book-card {
  cursor: pointer;
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

.book-title {
  font-family: var(--yc-font-display);
  font-size: 15px;
  font-weight: 600;
  margin: 0 0 4px;
  color: var(--yc-text);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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
  margin-top: 6px;
  gap: 8px;
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
