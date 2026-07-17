<template>
  <div class="book-edit-page">
    <div class="page-header">
      <el-button @click="$router.back()" :icon="ArrowLeft" text>
        返回
      </el-button>
      <h2>{{ bookId ? '编辑图书' : '添加图书' }}</h2>
    </div>

    <el-card>
      <el-tabs v-model="activeTab" type="border-card">
        <!-- 图书基本信息标签页 -->
        <el-tab-pane label="基本信息" name="basic">
          <el-form :model="bookForm" :rules="bookRules" ref="bookFormRef" label-width="100px" style="padding: 20px;">
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item label="标题" prop="title">
                  <el-input v-model="bookForm.title" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="作者" prop="author">
                  <el-input v-model="bookForm.author" />
                </el-form-item>
              </el-col>
            </el-row>
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item label="ISBN" prop="isbn">
                  <el-input v-model="bookForm.isbn" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="分类" prop="category_id">
                  <el-select v-model="bookForm.category_id" placeholder="选择分类" style="width: 100%">
                    <el-option
                      v-for="cat in categories"
                      :key="cat.id"
                      :label="cat.name"
                      :value="cat.id"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item label="总数量" prop="total_copies">
                  <el-input-number v-model="bookForm.total_copies" :min="1" style="width: 100%" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="售价" prop="price">
                  <el-input-number v-model="bookForm.price" :min="0" :precision="2" :step="0.01" style="width: 100%">
                    <template #prefix>¥</template>
                  </el-input-number>
                  <div style="color: #909399; font-size: 12px; margin-top: 5px;">可选，留空表示未设置售价</div>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item label="封面" prop="cover">
                  <el-input v-model="bookForm.cover" placeholder="自有封面 URL（可选，勿填第三方版权图）" />
                  <div style="margin-top: 10px;">
                    <BookCover
                      :book="{ ...bookForm, id: route.params.id }"
                      :preview="false"
                      image-style="width: 120px; height: 160px; border-radius: 4px; border: 1px solid #dcdfe6;"
                    />
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="出版日期" prop="published_at">
                  <el-date-picker
                    v-model="bookForm.published_at"
                    type="date"
                    placeholder="选择出版日期"
                    style="width: 100%"
                    format="YYYY-MM-DD"
                    value-format="YYYY-MM-DD"
                  />
                </el-form-item>
              </el-col>
            </el-row>
            <el-form-item label="描述" prop="description">
              <el-input v-model="bookForm.description" type="textarea" :rows="3" />
            </el-form-item>
            <el-form-item label="内容简介" prop="content_intro">
              <el-input 
                v-model="bookForm.content_intro" 
                type="textarea" 
                :rows="6"
                placeholder="支持HTML格式，可输入富文本内容"
              />
              <div style="color: #909399; font-size: 12px; margin-top: 5px;">
                支持HTML标签，如：&lt;p&gt;段落&lt;/p&gt;、&lt;strong&gt;加粗&lt;/strong&gt;、&lt;em&gt;斜体&lt;/em&gt; 等
              </div>
            </el-form-item>
            <el-form-item label="作者简介" prop="author_intro">
              <el-input 
                v-model="bookForm.author_intro" 
                type="textarea" 
                :rows="6"
                placeholder="支持HTML格式，可输入富文本内容"
              />
              <div style="color: #909399; font-size: 12px; margin-top: 5px;">
                支持HTML标签，如：&lt;p&gt;段落&lt;/p&gt;、&lt;strong&gt;加粗&lt;/strong&gt;、&lt;em&gt;斜体&lt;/em&gt; 等
              </div>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveBook" :loading="saving">保存图书</el-button>
              <el-button @click="$router.back()">取消</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 章节管理标签页 -->
        <el-tab-pane label="章节管理" name="chapters" :disabled="!bookId">
          <div class="chapter-management" style="padding: 20px;">
            <div v-if="!bookId" class="chapter-tip">
              <el-alert
                title="提示"
                type="info"
                :closable="false"
                show-icon
              >
                <template #default>
                  <p>请先保存图书基本信息，然后才能管理章节。</p>
                </template>
              </el-alert>
            </div>
            <div v-else>
              <div class="chapter-toolbar">
                <el-button type="primary" @click="addChapter">
                  <el-icon><Plus /></el-icon>
                  添加章节
                </el-button>
                <el-button type="info" @click="loadChapters" :loading="chaptersLoading">
                  <el-icon><Refresh /></el-icon>
                  刷新
                </el-button>
              </div>

                  <el-tree
                    v-if="chapters.length > 0"
                    :data="chaptersTree"
                    :props="{ children: 'children', label: 'title' }"
                    default-expand-all
                    node-key="id"
                    class="chapter-tree"
                  >
                    <template #default="{ data }">
                      <div class="chapter-node">
                        <span class="chapter-title">{{ data.title }}</span>
                        <div class="chapter-actions">
                          <el-button size="small" @click="editChapter(data)">编辑标题</el-button>
                          <el-button size="small" type="primary" @click="editChapterContent(data)">编辑内容</el-button>
                          <el-button size="small" type="success" @click="addChildChapter(data)">子章节</el-button>
                          <el-button size="small" type="danger" @click="deleteChapter(data.id)">删除</el-button>
                        </div>
                      </div>
                    </template>
                  </el-tree>

              <el-empty v-else description="暂无章节，点击上方按钮添加" :image-size="100" />
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-card>

    <!-- 章节编辑对话框 -->
    <el-dialog v-model="showChapterEditDialog" :title="editingChapter ? '编辑章节' : '添加章节'" width="800px">
      <el-form :model="chapterForm" ref="chapterFormRef" label-width="100px">
        <el-form-item label="章节标题" required>
          <el-input v-model="chapterForm.title" placeholder="请输入章节标题" />
        </el-form-item>
        <el-form-item label="父章节">
          <el-select v-model="chapterForm.parent_id" placeholder="选择父章节（可选）" clearable style="width: 100%">
            <el-option
              v-for="chapter in availableParentChapters"
              :key="chapter.id"
              :label="chapter.title"
              :value="chapter.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="正文内容">
          <el-input
            v-model="chapterForm.content"
            type="textarea"
            :rows="15"
            placeholder="支持HTML格式，可输入富文本内容"
          />
          <div style="color: #909399; font-size: 12px; margin-top: 5px;">
            支持HTML标签，如：&lt;p&gt;段落&lt;/p&gt;、&lt;strong&gt;加粗&lt;/strong&gt;、&lt;em&gt;斜体&lt;/em&gt; 等
          </div>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showChapterEditDialog = false">取消</el-button>
        <el-button type="primary" @click="saveChapter">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { booksApi } from '@/api/books'
import { categoriesApi } from '@/api/categories'
import { chaptersApi } from '@/api/chapters'
import { ElMessage, ElMessageBox } from 'element-plus'
import { ArrowLeft, Plus, Refresh } from '@element-plus/icons-vue'
import BookCover from '@/components/BookCover.vue'
import { isBlockedCoverUrl } from '@/utils/bookCover'

const route = useRoute()
const router = useRouter()

const bookId = ref(route.params.id ? parseInt(route.params.id) : null)
const activeTab = ref('basic')
const saving = ref(false)
const bookFormRef = ref(null)
const categories = ref([])

// 章节管理相关
const showChapterEditDialog = ref(false)
const chapters = ref([])
const chaptersLoading = ref(false)
const editingChapter = ref(null)
const chapterFormRef = ref(null)
const chapterForm = reactive({
  title: '',
  parent_id: null,
  content: '',
})

const bookForm = reactive({
  title: '',
  author: '',
  isbn: '',
  category_id: null,
  total_copies: 1,
  price: null,
  description: '',
  content_intro: '',
  author_intro: '',
  cover: '',
  published_at: null,
})

const bookRules = {
  title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
  author: [{ required: true, message: '请输入作者', trigger: 'blur' }],
  isbn: [{ required: true, message: '请输入ISBN', trigger: 'blur' }],
  category_id: [{ required: true, message: '请选择分类', trigger: 'change' }],
  total_copies: [{ required: true, message: '请输入数量', trigger: 'blur' }],
}

onMounted(() => {
  loadCategories()
  if (bookId.value) {
    loadBook()
    loadChapters()
  }
})

const loadCategories = async () => {
  try {
    const res = await categoriesApi.getCategories()
    categories.value = res.data
  } catch (error) {
    ElMessage.error('加载分类失败')
  }
}

const loadBook = async () => {
  try {
    const res = await booksApi.getBook(bookId.value)
    const book = res.data?.data || res.data
    Object.assign(bookForm, {
      title: book.title,
      author: book.author,
      isbn: book.isbn,
      category_id: book.category?.id || book.category_id,
      total_copies: book.total_copies,
      price: book.price ? parseFloat(book.price) : null,
      description: book.description || '',
      content_intro: book.content_intro || '',
      author_intro: book.author_intro || '',
      cover: (book.cover && !book.cover.startsWith('data:') && !isBlockedCoverUrl(book.cover))
        ? book.cover
        : '',
      published_at: book.published_at || null,
    })
  } catch (error) {
    ElMessage.error('加载图书失败')
  }
}

const saveBook = async () => {
  if (!bookFormRef.value) return
  
  await bookFormRef.value.validate(async (valid) => {
    if (valid) {
      saving.value = true
      try {
        if (bookId.value) {
          await booksApi.updateBook(bookId.value, bookForm)
          ElMessage.success('更新成功')
        } else {
          const res = await booksApi.createBook(bookForm)
          const newBookId = res.data?.data?.id || res.data?.id
          bookId.value = newBookId
          ElMessage.success('添加成功，现在可以管理章节了')
          // 切换到章节管理标签页
          activeTab.value = 'chapters'
          // 加载章节
          await loadChapters()
          // 更新路由
          router.replace(`/admin/books/${newBookId}`)
        }
      } catch (error) {
        ElMessage.error(error.response?.data?.message || '操作失败')
      } finally {
        saving.value = false
      }
    }
  })
}

// 章节管理功能
const loadChapters = async () => {
  if (!bookId.value) return
  chaptersLoading.value = true
  try {
    const res = await chaptersApi.getChapters(bookId.value)
    const chaptersData = res.data.data || res.data || []
    // 确保 parent_id 类型一致（统一为数字或 null）
    chapters.value = chaptersData.map(ch => ({
      ...ch,
      parent_id: ch.parent_id ? parseInt(ch.parent_id) : null,
      id: parseInt(ch.id)
    }))
    console.log('加载的章节数据:', chapters.value)
    console.log('构建的树形结构:', chaptersTree.value)
  } catch (error) {
    console.error('加载章节失败:', error)
    ElMessage.error('加载章节失败')
  } finally {
    chaptersLoading.value = false
  }
}

const chaptersTree = computed(() => {
  if (!chapters.value || chapters.value.length === 0) return []
  
  const buildTree = (items, parentId = null) => {
    return items
      .filter(item => {
        // 确保类型匹配：null 或数字
        const itemParentId = item.parent_id ? parseInt(item.parent_id) : null
        return itemParentId === parentId
      })
      .map(item => ({
        ...item,
        children: buildTree(items, parseInt(item.id))
      }))
  }
  const tree = buildTree(chapters.value)
  return tree
})

const availableParentChapters = computed(() => {
  const excludeIds = editingChapter.value ? getChapterAndChildrenIds(editingChapter.value.id) : []
  return chapters.value.filter(ch => !excludeIds.includes(ch.id))
})

const getChapterAndChildrenIds = (chapterId) => {
  const ids = [chapterId]
  const findChildren = (parentId) => {
    chapters.value.forEach(ch => {
      if (ch.parent_id === parentId) {
        ids.push(ch.id)
        findChildren(ch.id)
      }
    })
  }
  findChildren(chapterId)
  return ids
}

const addChapter = () => {
  editingChapter.value = null
  chapterForm.title = ''
  chapterForm.parent_id = null
  chapterForm.content = ''
  showChapterEditDialog.value = true
}

const addChildChapter = (parentChapter) => {
  editingChapter.value = null
  chapterForm.title = ''
  chapterForm.parent_id = parentChapter.id
  chapterForm.content = ''
  showChapterEditDialog.value = true
}

const editChapter = (chapter) => {
  editingChapter.value = chapter
  chapterForm.title = chapter.title
  chapterForm.parent_id = chapter.parent_id
  chapterForm.content = chapter.content || ''
  showChapterEditDialog.value = true
}

const saveChapter = async () => {
  if (!chapterForm.title.trim()) {
    ElMessage.warning('请输入章节标题')
    return
  }

  if (!bookId.value) {
    ElMessage.warning('请先保存图书基本信息')
    return
  }

  try {
    if (editingChapter.value) {
      await chaptersApi.updateChapter(editingChapter.value.id, chapterForm)
      ElMessage.success('更新成功')
    } else {
      await chaptersApi.createChapter(bookId.value, chapterForm)
      ElMessage.success('添加成功')
    }
    showChapterEditDialog.value = false
    await loadChapters()
  } catch (error) {
    ElMessage.error(error.response?.data?.message || '操作失败')
  }
}

    const deleteChapter = async (chapterId) => {
      try {
        await ElMessageBox.confirm('确定要删除这个章节吗？删除后其所有子章节也会被删除。', '提示', {
          type: 'warning',
        })
        await chaptersApi.deleteChapter(chapterId)
        ElMessage.success('删除成功')
        await loadChapters()
      } catch (error) {
        if (error !== 'cancel') {
          ElMessage.error('删除失败')
        }
      }
    }

    // 编辑章节内容（跳转到新页面）
    const editChapterContent = (chapter) => {
      router.push(`/admin/books/${bookId.value}/chapters/${chapter.id}/edit`)
    }
</script>

<style scoped>
.book-edit-page {
  padding: 20px;
}

.page-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}

.page-header h2 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
}

/* 章节管理样式 */
.chapter-management {
  min-height: 400px;
}

.chapter-tip {
  margin: 20px 0;
}

.chapter-toolbar {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.chapter-tree {
  max-height: 500px;
  overflow-y: auto;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  padding: 10px;
  margin-top: 20px;
}

.chapter-node {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding: 5px 0;
}

.chapter-title {
  flex: 1;
  font-size: 14px;
  color: #303133;
}

.chapter-actions {
  display: flex;
  gap: 5px;
}
</style>
