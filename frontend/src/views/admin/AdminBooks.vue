<template>
  <div class="admin-books">
    <div class="page-header">
      <h2>图书管理</h2>
      <el-button type="primary" @click="$router.push('/admin/books/new')">添加图书</el-button>
    </div>

    <el-card>
      <div class="search-bar">
        <el-input
          v-model="searchKeyword"
          placeholder="搜索图书（标题、作者、ISBN）"
          style="width: 300px"
          @keyup.enter="loadBooks"
        >
          <template #append>
            <el-button @click="loadBooks">搜索</el-button>
          </template>
        </el-input>
        <el-select v-model="selectedCategory" placeholder="选择分类" clearable style="width: 200px" @change="loadBooks">
          <el-option
            v-for="cat in categories"
            :key="cat.id"
            :label="cat.name"
            :value="cat.id"
          />
        </el-select>
      </div>

      <el-table :data="books" style="width: 100%; margin-top: 20px" v-loading="loading">
        <el-table-column label="封面" width="100">
          <template #default="scope">
            <el-image
              v-if="scope.row.cover"
              :src="scope.row.cover"
              style="width: 60px; height: 80px; object-fit: cover; border-radius: 4px;"
              fit="cover"
              :preview-src-list="[scope.row.cover]"
            />
            <div v-else style="width: 60px; height: 80px; background: #f5f5f5; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999;">
              无封面
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="title" label="标题" />
        <el-table-column prop="author" label="作者" />
        <el-table-column prop="isbn" label="ISBN" />
        <el-table-column prop="category.name" label="分类" />
        <el-table-column prop="total_copies" label="总数量" />
        <el-table-column prop="available_copies" label="可借数量" />
        <el-table-column prop="price" label="售价">
          <template #default="scope">
            <span v-if="scope.row.price">¥{{ scope.row.price }}</span>
            <span v-else style="color: #999;">未设置</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <el-button size="small" @click="$router.push(`/admin/books/${scope.row.id}`)">编辑</el-button>
            <el-button size="small" type="danger" @click="deleteBook(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        v-model:current-page="currentPage"
        v-model:page-size="pageSize"
        :total="total"
        :page-sizes="[10, 15, 20, 50, 100]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        style="margin-top: 20px; justify-content: flex-end;"
      />
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { booksApi } from '@/api/books'
import { categoriesApi } from '@/api/categories'
import { ElMessage, ElMessageBox } from 'element-plus'

const loading = ref(false)
const books = ref([])
const categories = ref([])
const searchKeyword = ref('')
const selectedCategory = ref(null)
const currentPage = ref(1)
const pageSize = ref(15)
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
    total.value = res.data.total || res.data.meta?.total || 0
  } catch (error) {
    ElMessage.error('加载图书失败')
    console.error('加载图书失败:', error)
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

const deleteBook = async (id) => {
  try {
    await ElMessageBox.confirm('确定要删除这本图书吗？', '提示', {
      type: 'warning',
    })
    await booksApi.deleteBook(id)
    ElMessage.success('删除成功')
    loadBooks()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}
</script>

<style scoped>
.admin-books {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.search-bar {
  display: flex;
  gap: 10px;
}

:deep(.el-pagination) {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}
</style>
