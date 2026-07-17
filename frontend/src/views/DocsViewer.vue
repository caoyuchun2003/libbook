<template>
  <div class="docs-viewer">
    <el-header class="docs-header">
      <div class="header-content">
        <div class="header-left">
          <h2>📚 宇春书城项目文档</h2>
        </div>
        <div class="header-right">
          <el-button @click="goHome" type="primary" plain>返回首页</el-button>
        </div>
      </div>
    </el-header>
    <el-container style="height: calc(100% - 60px)">
      <el-aside width="250px" class="docs-sidebar">
        <div class="sidebar-header">
          <h3>📚 项目文档</h3>
        </div>
        <el-menu
          :default-active="activeDoc"
          @select="handleDocSelect"
          class="docs-menu"
        >
          <el-menu-item
            v-for="doc in docs"
            :key="doc.name"
            :index="doc.name"
          >
            <span>{{ doc.title }}</span>
          </el-menu-item>
        </el-menu>
      </el-aside>
      <el-main class="docs-content">
        <div v-loading="loading" class="markdown-body">
          <div v-if="!currentDoc && !loading && docs.length === 0" class="empty-state">
            <el-empty description="未找到文档文件">
              <el-button type="primary" @click="loadDocsList">重试</el-button>
              <div style="margin-top: 20px; color: #909399; font-size: 14px;">
                <p>请确保文档文件存在于 <code>public/docs/</code> 目录下</p>
              </div>
            </el-empty>
          </div>
          <div v-else-if="!currentDoc && !loading && docs.length > 0" class="empty-state">
            <el-empty description="请从左侧选择要查看的文档" />
          </div>
          <div v-else-if="currentDoc" v-html="renderedContent"></div>
        </div>
      </el-main>
    </el-container>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { marked } from 'marked'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'
import { ElMessage } from 'element-plus'
import { useAuthStore } from '@/store/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const docs = ref([])
const currentDoc = ref(null)
const renderedContent = ref('')
const loading = ref(false)
const activeDoc = ref('')

// 配置 marked
marked.setOptions({
  highlight: function(code, lang) {
    if (lang && hljs.getLanguage(lang)) {
      try {
        return hljs.highlight(code, { language: lang }).value
      } catch (err) {}
    }
    return hljs.highlightAuto(code).value
  },
  breaks: true,
  gfm: true,
})

onMounted(() => {
  loadDocsList()
  
  // 如果有路由参数，加载对应文档
  if (route.params.filename) {
    loadDoc(route.params.filename)
  }
})

// 监听路由参数变化
watch(() => route.params.filename, (newFilename) => {
  if (newFilename && newFilename !== activeDoc.value) {
    loadDoc(newFilename)
  }
})

// 文档列表配置
const docList = [
  { name: 'PRD.md', title: '产品需求文档' },
  { name: 'DESIGN.md', title: '设计文档' },
  { name: 'API.md', title: 'API 接口文档' },
  { name: 'USER_GUIDE.md', title: '用户使用指南' },
]

const loadDocsList = async () => {
  try {
    // 验证哪些文档文件存在，并提取真实标题
    const existingDocs = []
    for (const doc of docList) {
      try {
        const response = await fetch(`/docs/${doc.name}`)
        if (response.ok) {
          const content = await response.text()
          // 从内容中提取标题
          const titleMatch = content.match(/^#\s+(.+)$/m)
          const title = titleMatch ? titleMatch[1].trim() : doc.title
          
          existingDocs.push({
            name: doc.name,
            title: title,
          })
        }
      } catch (e) {
        // 文件不存在，跳过
        console.warn(`文档文件不存在: ${doc.name}`)
      }
    }
    
    docs.value = existingDocs
    
    // 如果列表不为空且没有选中文档，默认选中第一个
    if (docs.value.length > 0 && !route.params.filename) {
      loadDoc(docs.value[0].name)
    }
  } catch (error) {
    ElMessage.error('加载文档列表失败')
    console.error(error)
  }
}

const loadDoc = async (filename) => {
  loading.value = true
  activeDoc.value = filename
  
  try {
    // 直接从 public/docs 目录读取文件
    const response = await fetch(`/docs/${filename}`)
    
    if (!response.ok) {
      throw new Error(`文件不存在: ${filename}`)
    }
    
    const content = await response.text()
    
    // 提取标题（从第一个 # 标题）
    const titleMatch = content.match(/^#\s+(.+)$/m)
    const title = titleMatch ? titleMatch[1].trim() : filename.replace('.md', '')
    
    currentDoc.value = {
      filename,
      title,
      content,
    }
    
    renderedContent.value = marked.parse(content)
    
    // 更新路由但不刷新页面
    router.replace({ params: { filename } }).catch(() => {})
  } catch (error) {
    ElMessage.error(`加载文档失败: ${error.message}`)
    console.error(error)
  } finally {
    loading.value = false
  }
}

const handleDocSelect = (filename) => {
  loadDoc(filename)
}

const goHome = () => {
  if (authStore.isAuthenticated) {
    router.push('/')
  } else {
    router.push('/login')
  }
}
</script>

<style scoped>
.docs-viewer {
  height: 100vh;
  overflow: hidden;
  margin: 0;
  padding: 0;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #fff;
}

.docs-header {
  background-color: var(--yc-ink);
  color: white;
  padding: 0 20px;
  height: 60px;
  line-height: 60px;
  border-bottom: 1px solid #e4e7ed;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
}

.header-left h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 500;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.docs-sidebar {
  background-color: #f5f5f5;
  border-right: 1px solid #e4e7ed;
  overflow-y: auto;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #e4e7ed;
  background-color: white;
}

.sidebar-header h3 {
  margin: 0;
  font-size: 18px;
  color: #303133;
}

.docs-menu {
  border: none;
  background-color: transparent;
}

.docs-content {
  padding: 40px;
  overflow-y: auto;
  background-color: #fff;
}

.empty-state {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

:deep(.markdown-body) {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  line-height: 1.6;
  color: #24292e;
  max-width: 900px;
  margin: 0 auto;
}

:deep(.markdown-body h1) {
  font-size: 2em;
  border-bottom: 1px solid #eaecef;
  padding-bottom: 0.3em;
  margin-top: 24px;
  margin-bottom: 16px;
  font-weight: 600;
}

:deep(.markdown-body h2) {
  font-size: 1.5em;
  border-bottom: 1px solid #eaecef;
  padding-bottom: 0.3em;
  margin-top: 24px;
  margin-bottom: 16px;
  font-weight: 600;
}

:deep(.markdown-body h3) {
  font-size: 1.25em;
  margin-top: 24px;
  margin-bottom: 16px;
  font-weight: 600;
}

:deep(.markdown-body h4) {
  font-size: 1em;
  margin-top: 24px;
  margin-bottom: 16px;
  font-weight: 600;
}

:deep(.markdown-body p) {
  margin-bottom: 16px;
}

:deep(.markdown-body ul),
:deep(.markdown-body ol) {
  margin-bottom: 16px;
  padding-left: 2em;
}

:deep(.markdown-body li) {
  margin-bottom: 0.25em;
}

:deep(.markdown-body code) {
  padding: 0.2em 0.4em;
  margin: 0;
  font-size: 85%;
  background-color: rgba(27, 31, 35, 0.05);
  border-radius: 3px;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
}

:deep(.markdown-body pre) {
  padding: 16px;
  overflow: auto;
  font-size: 85%;
  line-height: 1.45;
  background-color: #f6f8fa;
  border-radius: 6px;
  margin-bottom: 16px;
}

:deep(.markdown-body pre code) {
  display: inline;
  max-width: auto;
  padding: 0;
  margin: 0;
  overflow: visible;
  line-height: inherit;
  word-wrap: normal;
  background-color: transparent;
  border: 0;
}

:deep(.markdown-body table) {
  border-collapse: collapse;
  margin-bottom: 16px;
  width: 100%;
}

:deep(.markdown-body table th),
:deep(.markdown-body table td) {
  padding: 6px 13px;
  border: 1px solid #dfe2e5;
}

:deep(.markdown-body table th) {
  font-weight: 600;
  background-color: #f6f8fa;
}

:deep(.markdown-body blockquote) {
  padding: 0 1em;
  color: #6a737d;
  border-left: 0.25em solid #dfe2e5;
  margin-bottom: 16px;
}

:deep(.markdown-body a) {
  color: #0366d6;
  text-decoration: none;
}

:deep(.markdown-body a:hover) {
  text-decoration: underline;
}

:deep(.markdown-body img) {
  max-width: 100%;
  box-sizing: content-box;
  background-color: #fff;
}

:deep(.markdown-body hr) {
  height: 0.25em;
  padding: 0;
  margin: 24px 0;
  background-color: #e1e4e8;
  border: 0;
}
</style>
