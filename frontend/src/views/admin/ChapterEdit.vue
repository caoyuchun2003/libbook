<template>
  <div class="chapter-edit-page" v-loading="loading">
    <div class="page-header">
      <el-page-header @back="goBack" :title="'编辑章节'">
        <template #content>
          <span class="page-header-title">编辑章节：{{ chapter?.title || '加载中...' }}</span>
        </template>
        <template #extra>
          <el-button @click="goBack">返回</el-button>
          <el-button type="primary" @click="saveChapter" :loading="saving">保存</el-button>
        </template>
      </el-page-header>
    </div>

    <div class="edit-container">
      <!-- 左侧章节列表 -->
      <div class="chapters-sidebar">
        <div class="sidebar-header">
          <el-icon><List /></el-icon>
          <span>章节列表</span>
        </div>
        <div class="sidebar-content">
          <div 
            v-for="ch in allChaptersFlat" 
            :key="ch.id" 
            class="chapter-item"
            :class="{ 
              'active': currentChapterId === ch.id,
              'level-2': ch.level > 1 
            }"
            @click="switchChapter(ch.id)"
          >
            <span class="chapter-item-title">{{ ch.title }}</span>
          </div>
        </div>
      </div>

      <!-- 右侧编辑区域 -->
      <div class="edit-main">
        <el-card class="main-card" v-if="chapter">
          <el-form :model="chapterForm" ref="chapterFormRef" label-width="100px" class="chapter-form">
            <el-form-item label="章节标题" required>
              <el-input v-model="chapterForm.title" placeholder="请输入章节标题" />
            </el-form-item>

            <el-form-item label="正文内容">
              <!-- 编辑器容器 -->
              <div class="editor-container-wrapper">
                <!-- 工具栏和统计信息 -->
                <div class="editor-toolbar-wrapper">
                  <div class="toolbar-info">
                    <span class="word-count">字数：{{ wordCount }}</span>
                    <el-divider direction="vertical" />
                    <span class="auto-save-status" :class="{ 'saved': autoSaveStatus === 'saved', 'saving': autoSaveStatus === 'saving', 'error': autoSaveStatus === 'error' }">
                      {{ autoSaveStatusText }}
                    </span>
                  </div>
                </div>

                <!-- Tiptap 编辑器工具栏 -->
                <TiptapToolbar v-if="editor" :editor="editor" />

                <!-- Tiptap 编辑器内容区 -->
                <div class="editor-wrapper">
                  <EditorContent :editor="editor" class="editor-content" />
                </div>
              </div>
            </el-form-item>
          </el-form>
        </el-card>

        <el-empty v-else description="章节信息加载失败或不存在" :image-size="200" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, computed, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import { Table } from '@tiptap/extension-table'
import { TableRow } from '@tiptap/extension-table-row'
import { TableCell } from '@tiptap/extension-table-cell'
import { TableHeader } from '@tiptap/extension-table-header'
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'
import { createLowlight } from 'lowlight'
import javascript from 'highlight.js/lib/languages/javascript'
import typescript from 'highlight.js/lib/languages/typescript'
import html from 'highlight.js/lib/languages/xml'
import css from 'highlight.js/lib/languages/css'
import json from 'highlight.js/lib/languages/json'
import python from 'highlight.js/lib/languages/python'
import java from 'highlight.js/lib/languages/java'
import cpp from 'highlight.js/lib/languages/cpp'
import sql from 'highlight.js/lib/languages/sql'
import bash from 'highlight.js/lib/languages/bash'

const lowlight = createLowlight()
lowlight.register('javascript', javascript)
lowlight.register('typescript', typescript)
lowlight.register('html', html)
lowlight.register('css', css)
lowlight.register('json', json)
lowlight.register('python', python)
lowlight.register('java', java)
lowlight.register('cpp', cpp)
lowlight.register('sql', sql)
lowlight.register('bash', bash)
import { chaptersApi } from '@/api/chapters'
import { ElMessage, ElMessageBox } from 'element-plus'
import { List } from '@element-plus/icons-vue'
import TiptapToolbar from '@/components/TiptapToolbar.vue'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const saving = ref(false)
const chapter = ref(null)
const chapterFormRef = ref(null)
const allChapters = ref([])
const currentChapterId = ref(null)
const hasUnsavedChanges = ref(false)
const autoSaveStatus = ref('idle') // 'idle' | 'saving' | 'saved' | 'error'
const autoSaveTimer = ref(null)

const chapterForm = reactive({
  title: '',
  content: '',
})

// 是否正在设置内容（避免触发自动保存）
const isSettingContent = ref(false)

// 初始化 Tiptap 编辑器
const editor = useEditor({
  content: '',
  extensions: [
    StarterKit.configure({
      codeBlock: false, // 使用低亮版本
    }),
    Underline,
    Image.configure({
      inline: true,
      allowBase64: true,
    }),
    Link.configure({
      openOnClick: false,
      HTMLAttributes: {
        target: '_blank',
        rel: 'noopener noreferrer',
      },
    }),
    Table.configure({
      resizable: true,
    }),
    TableRow,
    TableHeader,
    TableCell,
    CodeBlockLowlight.configure({
      lowlight,
    }),
  ],
  onUpdate: ({ editor }) => {
    if (isSettingContent.value) {
      return
    }
    chapterForm.content = editor.getHTML()
    hasUnsavedChanges.value = true
    triggerAutoSave()
  },
  onCreate: ({ editor }) => {
    // 编辑器创建成功后的回调
    console.log('Tiptap editor created successfully')
    // 如果已经有章节数据，设置内容
    if (chapter.value && chapter.value.content) {
      isSettingContent.value = true
      editor.setContent(chapter.value.content)
      setTimeout(() => {
        isSettingContent.value = false
      }, 100)
    }
  },
  onDestroy: () => {
    console.log('Tiptap editor destroyed')
  },
})

// 字数统计
const wordCount = computed(() => {
  if (!editor.value) return 0
  const text = editor.value.getText()
  return text.length
})

// 自动保存状态文本
const autoSaveStatusText = computed(() => {
  const statusMap = {
    idle: '未保存',
    saving: '保存中...',
    saved: '已自动保存',
    error: '保存失败',
  }
  return statusMap[autoSaveStatus.value]
})

// 自动保存
const triggerAutoSave = () => {
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
  }
  
  autoSaveTimer.value = setTimeout(async () => {
    if (hasUnsavedChanges.value && chapterForm.title.trim() && currentChapterId.value) {
      autoSaveStatus.value = 'saving'
      try {
        await chaptersApi.updateChapter(currentChapterId.value, {
          title: chapterForm.title,
          content: chapterForm.content,
        })
        autoSaveStatus.value = 'saved'
        hasUnsavedChanges.value = false
        setTimeout(() => {
          if (autoSaveStatus.value === 'saved') {
            autoSaveStatus.value = 'idle'
          }
        }, 2000)
      } catch (error) {
        autoSaveStatus.value = 'error'
        console.error('自动保存失败:', error)
      }
    }
  }, 2000) // 2秒后自动保存
}

// 扁平化所有章节（用于侧边栏显示）
const allChaptersFlat = computed(() => {
  const flatten = (chapters, level = 1) => {
    const result = []
    chapters.forEach(ch => {
      result.push({ ...ch, level })
      if (ch.children && ch.children.length > 0) {
        result.push(...flatten(ch.children, level + 1))
      }
    })
    return result
  }
  return flatten(allChapters.value)
})

onMounted(async () => {
  await loadAllChapters()
  // 等待编辑器初始化完成
  await nextTick()
  // 如果编辑器还没初始化，等待一下
  if (!editor.value) {
    await new Promise(resolve => setTimeout(resolve, 300))
  }
  watchRoute()
})

// 监听路由变化
watch(() => route.params.chapterId, (newChapterId) => {
  if (newChapterId) {
    currentChapterId.value = parseInt(newChapterId)
    loadChapter()
  }
})

const watchRoute = () => {
  const chapterId = parseInt(route.params.chapterId)
  currentChapterId.value = chapterId
  loadChapter()
}

// 监听编辑器初始化，设置初始内容
watch(() => editor.value, (newEditor) => {
  if (newEditor && chapter.value && typeof newEditor.setContent === 'function') {
    isSettingContent.value = true
    newEditor.setContent(chapter.value.content || '')
    setTimeout(() => {
      isSettingContent.value = false
    }, 100)
  }
})

// 监听章节变化，更新编辑器内容
watch(() => chapter.value, async (newChapter) => {
  if (newChapter && editor.value && typeof editor.value.setContent === 'function') {
    await nextTick()
    const currentContent = editor.value.getHTML()
    const newContent = newChapter.content || ''
    if (currentContent !== newContent) {
      isSettingContent.value = true
      editor.value.setContent(newContent)
      setTimeout(() => {
        isSettingContent.value = false
      }, 100)
    }
  }
})

// 监听表单变化
watch(() => [chapterForm.title], () => {
  if (chapter.value) {
    hasUnsavedChanges.value = chapterForm.title !== chapter.value.title
  }
})

// 加载所有章节
const loadAllChapters = async () => {
  try {
    const bookId = route.params.bookId
    const res = await chaptersApi.getChapters(bookId)
    const flatChapters = (res.data.data || res.data || []).map(ch => ({
      ...ch,
      id: parseInt(ch.id),
      parent_id: ch.parent_id ? parseInt(ch.parent_id) : null,
    })).sort((a, b) => a.order - b.order)
    
    // 构建树形结构
    const buildTree = (items, parentId = null) => {
      return items
        .filter(item => item.parent_id === parentId)
        .map(item => ({
          ...item,
          children: buildTree(items, item.id)
        }))
        .sort((a, b) => a.order - b.order)
    }
    
    allChapters.value = buildTree(flatChapters)
  } catch (error) {
    console.error('加载章节列表失败:', error)
    ElMessage.error('加载章节列表失败')
  }
}

// 加载单个章节
const loadChapter = async () => {
  loading.value = true
  try {
    const chapterId = currentChapterId.value
    
    // 递归查找章节（包括子章节）
    const findChapter = (chapters, targetId) => {
      for (const ch of chapters) {
        if (ch.id === targetId) {
          return ch
        }
        if (ch.children && ch.children.length > 0) {
          const found = findChapter(ch.children, targetId)
          if (found) return found
        }
      }
      return null
    }
    
    const foundChapter = findChapter(allChapters.value, chapterId)
    
    if (foundChapter) {
      chapter.value = foundChapter
      chapterForm.title = foundChapter.title
      chapterForm.content = foundChapter.content || ''
      hasUnsavedChanges.value = false
      
      // 等待编辑器初始化完成后再设置内容
      await nextTick()
      
      // 使用重试机制确保编辑器已初始化
      const setEditorContent = (content) => {
        if (editor.value && typeof editor.value.setContent === 'function') {
          try {
            isSettingContent.value = true
            editor.value.setContent(content || '')
            setTimeout(() => {
              isSettingContent.value = false
            }, 100)
            return true
          } catch (error) {
            console.error('设置编辑器内容失败:', error)
            isSettingContent.value = false
            return false
          }
        }
        return false
      }
      
      // 尝试设置内容
      if (!setEditorContent(foundChapter.content || '')) {
        // 如果编辑器还没初始化，使用重试机制
        let retries = 0
        const maxRetries = 20 // 最多重试2秒
        const retryInterval = setInterval(() => {
          retries++
          if (setEditorContent(foundChapter.content || '') || retries >= maxRetries) {
            clearInterval(retryInterval)
            if (retries >= maxRetries) {
              console.warn('编辑器初始化超时，内容将在编辑器就绪后自动加载')
            }
          }
        }, 100)
      }
    } else {
      ElMessage.error('章节不存在')
    }
  } catch (error) {
    console.error('加载章节失败:', error)
    ElMessage.error('加载章节失败')
  } finally {
    loading.value = false
  }
}

// 切换章节
const switchChapter = async (chapterId) => {
  if (chapterId === currentChapterId.value) {
    return
  }

  // 检查是否有未保存的更改
  if (hasUnsavedChanges.value) {
    try {
      await ElMessageBox.confirm(
        '当前章节有未保存的更改，是否保存后再切换？',
        '提示',
        {
          confirmButtonText: '保存并切换',
          cancelButtonText: '不保存切换',
          distinguishCancelAndClose: true,
          type: 'warning',
        }
      )
      // 用户选择保存
      await saveChapter(false) // 不返回，继续切换
    } catch (error) {
      if (error === 'cancel') {
        // 用户选择不保存，直接切换
      } else {
        // 用户取消操作
        return
      }
    }
  }

  // 切换章节
  currentChapterId.value = chapterId
  router.replace(`/admin/books/${route.params.bookId}/chapters/${chapterId}/edit`)
  await loadChapter()
}

const saveChapter = async (shouldReturn = true) => {
  if (!chapterForm.title.trim()) {
    ElMessage.warning('请输入章节标题')
    return false
  }

  saving.value = true
  try {
    const chapterId = currentChapterId.value
    
    // 从编辑器获取最新内容
    if (editor.value) {
      chapterForm.content = editor.value.getHTML()
    }
    
    await chaptersApi.updateChapter(chapterId, {
      title: chapterForm.title,
      content: chapterForm.content,
    })
    
    ElMessage.success('保存成功')
    hasUnsavedChanges.value = false
    autoSaveStatus.value = 'saved'
    
    // 更新章节数据
    await loadAllChapters()
    await loadChapter()
    
    if (shouldReturn) {
      // 返回图书编辑页面
      router.push(`/admin/books/${route.params.bookId}`)
    }
    return true
  } catch (error) {
    console.error('保存章节失败:', error)
    ElMessage.error(error.response?.data?.message || '保存失败')
    return false
  } finally {
    saving.value = false
  }
}

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
  }
})

const goBack = () => {
  const bookId = route.params.bookId
  router.push(`/admin/books/${bookId}`)
}
</script>

<style scoped>
.chapter-edit-page {
  padding: 20px;
  min-height: calc(100vh - 100px);
}

.edit-container {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}

/* 左侧章节列表 */
.chapters-sidebar {
  width: 280px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: calc(100vh - 180px);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 100px;
}

.sidebar-header {
  padding: 15px 20px;
  border-bottom: 1px solid #e4e7ed;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.sidebar-content {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
}

.chapter-item {
  padding: 10px 12px;
  font-size: 14px;
  color: #606266;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.3s;
  margin-bottom: 4px;
}

.chapter-item:hover {
  background: #f0f7ff;
  color: #409eff;
}

.chapter-item.active {
  background: #e6f4ff;
  color: #409eff;
  font-weight: 600;
}

.chapter-item.level-2 {
  padding-left: 28px;
  font-size: 13px;
}

.chapter-item-title {
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* 右侧编辑区域 */
.edit-main {
  flex: 1;
  min-width: 0;
}

.page-header {
  margin-bottom: 20px;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 15px;
}

.page-header-title {
  font-size: 20px;
  font-weight: 600;
  color: #303133;
}

.main-card {
  margin-top: 20px;
}

.chapter-form {
  padding: 20px;
}

/* 编辑器容器包装器 */
.editor-container-wrapper {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  background: #fff;
  overflow: hidden;
}

/* 工具栏和统计信息 */
.editor-toolbar-wrapper {
  border-bottom: 1px solid #e4e7ed;
}

.toolbar-info {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #fafbfc;
  font-size: 12px;
}

.word-count {
  color: #606266;
  font-weight: 500;
}

.auto-save-status {
  color: #909399;
  transition: color 0.3s;
  font-weight: 500;
}

.auto-save-status.saving {
  color: #409eff;
}

.auto-save-status.saved {
  color: #67c23a;
}

.auto-save-status.error {
  color: #f56c6c;
}

/* Tiptap 编辑器样式 */
.editor-wrapper {
  min-height: 500px;
  background: #fff;
}

.editor-content {
  padding: 20px;
  min-height: 500px;
}

/* Tiptap 编辑器内容样式 */
.editor-content :deep(.ProseMirror) {
  outline: none;
  min-height: 500px;
}

.editor-content :deep(.ProseMirror p) {
  margin: 12px 0;
  line-height: 1.8;
}

.editor-content :deep(.ProseMirror h1) {
  font-size: 2em;
  font-weight: 700;
  margin: 24px 0 16px 0;
  line-height: 1.2;
}

.editor-content :deep(.ProseMirror h2) {
  font-size: 1.5em;
  font-weight: 600;
  margin: 20px 0 14px 0;
  line-height: 1.3;
}

.editor-content :deep(.ProseMirror h3) {
  font-size: 1.25em;
  font-weight: 600;
  margin: 18px 0 12px 0;
  line-height: 1.4;
}

.editor-content :deep(.ProseMirror h4) {
  font-size: 1.1em;
  font-weight: 600;
  margin: 16px 0 10px 0;
}

.editor-content :deep(.ProseMirror ul),
.editor-content :deep(.ProseMirror ol) {
  padding-left: 30px;
  margin: 12px 0;
}

.editor-content :deep(.ProseMirror li) {
  margin: 6px 0;
}

.editor-content :deep(.ProseMirror blockquote) {
  border-left: 3px solid #409eff;
  padding: 10px 16px;
  margin: 16px 0;
  background: #f0f7ff;
  border-radius: 4px;
  color: #606266;
}

.editor-content :deep(.ProseMirror code) {
  background: #f5f7fa;
  padding: 2px 6px;
  border-radius: 3px;
  font-family: 'Monaco', 'Menlo', monospace;
  font-size: 0.9em;
  color: #e83e8c;
}

.editor-content :deep(.ProseMirror pre) {
  background: #282c34;
  color: #abb2bf;
  padding: 16px;
  border-radius: 6px;
  margin: 16px 0;
  overflow-x: auto;
}

.editor-content :deep(.ProseMirror pre code) {
  background: transparent;
  padding: 0;
  color: inherit;
  font-size: 0.9em;
}

.editor-content :deep(.ProseMirror img) {
  max-width: 100%;
  height: auto;
  border-radius: 6px;
  margin: 16px 0;
}

.editor-content :deep(.ProseMirror table) {
  border-collapse: collapse;
  margin: 16px 0;
  width: 100%;
}

.editor-content :deep(.ProseMirror table td),
.editor-content :deep(.ProseMirror table th) {
  border: 1px solid #dcdfe6;
  padding: 8px 12px;
  min-width: 100px;
}

.editor-content :deep(.ProseMirror table th) {
  background: #f5f7fa;
  font-weight: 600;
}

.editor-content :deep(.ProseMirror a) {
  color: #409eff;
  text-decoration: underline;
  cursor: pointer;
}

.editor-content :deep(.ProseMirror a:hover) {
  color: #66b1ff;
}

.editor-content :deep(.ProseMirror hr) {
  border: none;
  border-top: 2px solid #e4e7ed;
  margin: 24px 0;
}

.rich-content {
  line-height: 1.9;
  color: #303133;
  font-size: 16px;
  text-align: justify;
}

.rich-content :deep(p) {
  margin: 14px 0;
  text-align: justify;
}

.rich-content :deep(h1) {
  font-size: 26px;
  font-weight: 700;
  color: #303133;
  margin: 28px 0 18px 0;
  padding-bottom: 10px;
  border-bottom: 2px solid #e4e7ed;
}

.rich-content :deep(h2) {
  font-size: 24px;
  font-weight: 600;
  color: #303133;
  margin: 24px 0 16px 0;
}

.rich-content :deep(h3) {
  font-size: 20px;
  font-weight: 600;
  color: #303133;
  margin: 20px 0 14px 0;
}

.rich-content :deep(ul),
.rich-content :deep(ol) {
  margin: 16px 0;
  padding-left: 32px;
}

.rich-content :deep(li) {
  margin: 10px 0;
  line-height: 1.8;
}

.rich-content :deep(strong) {
  color: #303133;
  font-weight: 600;
}

.rich-content :deep(em) {
  font-style: italic;
  color: #606266;
}

.rich-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 20px 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.rich-content :deep(blockquote) {
  border-left: 3px solid #409eff;
  padding: 12px 18px;
  margin: 20px 0;
  background: #f0f7ff;
  border-radius: 4px;
  color: #606266;
  font-style: italic;
  font-size: 15px;
}

/* 响应式设计 */
@media (max-width: 992px) {
  .edit-container {
    flex-direction: column;
  }

  .chapters-sidebar {
    width: 100%;
    height: auto;
    position: relative;
    top: 0;
    max-height: 300px;
    margin-bottom: 20px;
  }
}

@media (max-width: 768px) {
  .chapter-edit-page {
    padding: 15px;
  }

  .edit-container {
    gap: 15px;
  }

  .chapters-sidebar {
    max-height: 250px;
  }

  .chapter-form {
    padding: 15px;
  }

  .content-preview {
    min-height: 200px;
    max-height: 400px;
    padding: 15px;
  }

  .rich-content {
    font-size: 15px;
  }
}
</style>
