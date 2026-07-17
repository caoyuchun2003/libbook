<template>
  <div class="my-bookshelf">
    <h2>我的书架</h2>
    <el-empty v-if="books.length === 0" description="书架空空如也，快去书城看看吧~" :image-size="200">
      <el-button type="primary" size="large" @click="$router.push('/books')">去书城</el-button>
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
          <BookCover :book="book" class="book-cover" />
        </div>
        <div class="book-info">
          <h3 class="book-title">{{ book.title }}</h3>
          <p class="book-author">{{ book.author }}</p>
          <div class="book-meta">
            <el-tag size="small">{{ book.category?.name }}</el-tag>
            <span v-if="book.price" class="book-price">¥{{ book.price }}</span>
          </div>
        </div>
      </el-card>
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
    // 这里可以后续扩展为获取用户收藏的图书
    // 目前先显示所有图书作为示例
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
  padding: 30px;
  min-height: calc(100vh - 120px);
  width: 100%;
  max-width: 100%;
}

h2 {
  margin-bottom: 30px;
  color: #303133;
  font-size: 36px;
  font-weight: 700;
  letter-spacing: 1px;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 30px;
  margin-top: 30px;
  width: 100%;
}

.book-card {
  cursor: pointer;
  transition: all 0.3s;
  border-radius: 12px;
  overflow: hidden;
  padding: 20px;
  width: 100%;
  min-width: 220px;
}

.book-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.book-cover-wrapper {
  width: 100%;
  padding-top: 150%;
  position: relative;
  margin-bottom: 16px;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.book-cover {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

.book-info {
  text-align: center;
  padding: 0 8px;
}

.book-title {
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 8px 0;
  color: #303133;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  line-height: 1.4;
}

.book-author {
  font-size: 15px;
  color: #909399;
  margin: 0 0 12px 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.book-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
}

.book-meta :deep(.el-tag) {
  font-size: 14px;
  padding: 6px 12px;
}

.book-price {
  font-size: 18px;
  color: #f56c6c;
  font-weight: bold;
}

@media (max-width: 1400px) {
  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }
}

@media (max-width: 1200px) {
  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 25px;
  }
}

@media (max-width: 768px) {
  .my-bookshelf {
    padding: 20px;
  }

  h2 {
    font-size: 28px;
    margin-bottom: 20px;
  }

  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }

  .book-card {
    min-width: 160px;
  }

  .book-title {
    font-size: 16px;
  }

  .book-author {
    font-size: 14px;
  }

  .book-price {
    font-size: 16px;
  }
}
</style>
