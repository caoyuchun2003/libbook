<template>
  <div class="book-reader-page" v-loading="loading">
    <div class="reader-header">
      <el-button 
        @click="goBack" 
        :icon="ArrowLeft" 
        class="back-button"
        text
      >
        返回详情
      </el-button>
      <div class="book-title-header">
        <h2>{{ book?.title }}</h2>
      </div>
    </div>

    <div v-if="book && chapters.length > 0" class="reader-container">
      <!-- 侧边栏目录 -->
      <div class="reader-sidebar">
        <div class="sidebar-header">
          <el-icon><List /></el-icon>
          <span>目录</span>
        </div>
        <div class="sidebar-content">
          <div 
            v-for="chapter in topLevelChapters" 
            :key="chapter.id" 
            class="sidebar-chapter-item"
          >
            <div 
              class="sidebar-chapter-title"
              :class="{ 
                'active': currentChapterId === chapter.id,
                'has-children': chapter.children && chapter.children.length > 0 
              }"
              @click="goToChapter(chapter.id)"
            >
              <el-icon 
                v-if="chapter.children && chapter.children.length > 0" 
                class="expand-icon"
                :class="{ 'expanded': expandedChapters.has(chapter.id) }"
                @click.stop="toggleChapter(chapter.id)"
              >
                <ArrowRight />
              </el-icon>
              <span class="chapter-title-text">{{ chapter.title }}</span>
            </div>
            <transition name="slide-fade">
              <div 
                v-if="chapter.children && chapter.children.length > 0 && expandedChapters.has(chapter.id)" 
                class="sidebar-children"
              >
                <div 
                  v-for="child in chapter.children" 
                  :key="child.id" 
                  class="sidebar-child-item"
                  :class="{ 'active': currentChapterId === child.id }"
                  @click="goToChapter(child.id)"
                >
                  <span>{{ child.title }}</span>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </div>

      <!-- 主内容区 -->
      <div class="reader-main">
        <div v-if="currentChapter" class="chapter-content-wrapper">
          <h1 class="chapter-title">{{ currentChapter.title }}</h1>
          <div 
            v-if="currentChapter.content && currentChapter.content.trim()" 
            class="chapter-content rich-content" 
            v-html="currentChapter.content"
          ></div>
          <el-empty v-else description="本章节暂无内容" :image-size="100" />
        </div>
        <el-empty v-else description="请选择章节开始阅读" :image-size="200" />
      </div>
    </div>
    <el-empty v-else description="图书信息加载失败或不存在" :image-size="200" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { booksApi } from '@/api/books'
import { chaptersApi } from '@/api/chapters'
import { ElMessage } from 'element-plus'
import { ArrowLeft, List, ArrowRight } from '@element-plus/icons-vue'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const book = ref(null)
const chapters = ref([])
const expandedChapters = ref(new Set())
const currentChapterId = ref(null)

onMounted(() => {
  loadBook()
  loadChapters()
  
  // 如果有章节ID参数，跳转到该章节
  if (route.query.chapterId) {
    currentChapterId.value = parseInt(route.query.chapterId)
  }
})

// 监听路由变化
watch(() => route.query.chapterId, (newChapterId) => {
  if (newChapterId) {
    currentChapterId.value = parseInt(newChapterId)
  }
})

const loadBook = async () => {
  loading.value = true
  try {
    const res = await booksApi.getBook(route.params.id)
    book.value = res.data?.data || res.data
  } catch (error) {
    console.error('加载图书详情失败:', error)
    ElMessage.error('加载图书详情失败')
  } finally {
    loading.value = false
  }
}

// 加载章节数据
const loadChapters = async () => {
  try {
    const res = await chaptersApi.getChapters(route.params.id)
    const flatChapters = (res.data.data || res.data || []).map(ch => ({
      ...ch,
      id: parseInt(ch.id),
      parent_id: ch.parent_id ? parseInt(ch.parent_id) : null,
    })).sort((a, b) => a.order - b.order)
    
    chapters.value = buildChapterTree(flatChapters)
    
    // 如果没有指定章节，默认显示第一个章节
    if (!currentChapterId.value && flatChapters.length > 0) {
      currentChapterId.value = flatChapters[0].id
    }
  } catch (error) {
    console.error('加载章节失败:', error)
    ElMessage.error('加载章节失败')
  }
}

// 构建章节树
const buildChapterTree = (flatChapters) => {
  const buildTree = (items, parentId = null) => {
    return items
      .filter(item => item.parent_id === parentId)
      .map(item => ({
        ...item,
        children: buildTree(items, item.id)
      }))
      .sort((a, b) => a.order - b.order)
  }
  return buildTree(flatChapters)
}

// 获取顶级章节
const topLevelChapters = computed(() => {
  return chapters.value.filter(ch => ch.parent_id === null)
})

// 获取当前章节
const currentChapter = computed(() => {
  if (!currentChapterId.value) return null
  const findChapter = (chapters) => {
    for (const chapter of chapters) {
      if (chapter.id === currentChapterId.value) {
        return chapter
      }
      if (chapter.children && chapter.children.length > 0) {
        const found = findChapter(chapter.children)
        if (found) return found
      }
    }
    return null
  }
  return findChapter(chapters.value)
})

// 切换章节展开/折叠
const toggleChapter = (chapterId) => {
  if (expandedChapters.value.has(chapterId)) {
    expandedChapters.value.delete(chapterId)
  } else {
    expandedChapters.value.add(chapterId)
  }
}

// 跳转到指定章节
const goToChapter = (chapterId) => {
  currentChapterId.value = chapterId
  // 更新URL参数
  router.replace({
    query: { ...route.query, chapterId }
  })
  // 滚动到顶部
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// 返回详情页
const goBack = () => {
  router.push(`/books/${route.params.id}`)
}
</script>

<style scoped>
.book-reader-page {
  min-height: 100vh;
  background:
    radial-gradient(ellipse at top, rgba(22, 58, 60, 0.04), transparent 50%),
    var(--yc-paper);
}

.reader-header {
  background: var(--yc-surface);
  padding: 14px 28px;
  border-bottom: 1px solid var(--yc-line);
  display: flex;
  align-items: center;
  gap: 20px;
  position: sticky;
  top: 0;
  z-index: 100;
}

.back-button {
  color: var(--yc-ink-soft);
  font-size: 15px;
  padding: 8px 0;
  transition: color 0.2s, transform 0.2s;
}

.back-button:hover {
  color: var(--yc-ink);
  transform: translateX(-4px);
}

.book-title-header h2 {
  margin: 0;
  font-family: var(--yc-font-display);
  font-size: 18px;
  font-weight: 600;
  color: var(--yc-text);
  letter-spacing: 0.04em;
}

.reader-container {
  display: flex;
  max-width: 1180px;
  margin: 0 auto;
  padding: 24px;
  gap: 20px;
}

.reader-sidebar {
  width: 260px;
  background: var(--yc-surface);
  border-radius: 8px;
  border: 1px solid var(--yc-line);
  height: calc(100vh - 100px);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 72px;
}

.sidebar-header {
  padding: 18px;
  border-bottom: 1px solid var(--yc-line);
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  color: var(--yc-text);
  letter-spacing: 0.06em;
}

.sidebar-content {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
}

.sidebar-chapter-item {
  margin-bottom: 4px;
}

.sidebar-chapter-title {
  padding: 10px 12px;
  font-size: 14px;
  color: var(--yc-ink-soft);
  cursor: pointer;
  border-radius: 6px;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background 0.2s, color 0.2s;
}

.sidebar-chapter-title:hover {
  background: rgba(22, 58, 60, 0.06);
  color: var(--yc-ink);
}

.sidebar-chapter-title.active {
  background: rgba(22, 58, 60, 0.1);
  color: var(--yc-ink);
  font-weight: 600;
}

.sidebar-chapter-title.has-children {
  font-weight: 500;
}

.expand-icon {
  font-size: 12px;
  color: var(--yc-muted);
  transition: transform 0.3s ease;
  flex-shrink: 0;
}

.expand-icon.expanded {
  transform: rotate(90deg);
  color: var(--yc-ink);
}

.chapter-title-text {
  flex: 1;
}

.sidebar-children {
  margin-left: 20px;
  margin-top: 4px;
  padding-left: 12px;
  border-left: 2px solid var(--yc-line);
}

.sidebar-child-item {
  padding: 8px 12px;
  font-size: 13px;
  color: var(--yc-ink-soft);
  cursor: pointer;
  border-radius: 6px;
  transition: background 0.2s, color 0.2s;
}

.sidebar-child-item:hover {
  background: rgba(22, 58, 60, 0.06);
  color: var(--yc-ink);
}

.sidebar-child-item.active {
  background: rgba(22, 58, 60, 0.1);
  color: var(--yc-ink);
  font-weight: 500;
}

.reader-main {
  flex: 1;
  background: var(--yc-surface);
  border-radius: 8px;
  border: 1px solid var(--yc-line);
  padding: 40px 48px;
  min-height: calc(100vh - 100px);
}

.chapter-content-wrapper {
  max-width: 720px;
  margin: 0 auto;
}

.chapter-title {
  font-family: var(--yc-font-display);
  font-size: 28px;
  font-weight: 700;
  color: var(--yc-text);
  margin: 0 0 28px;
  padding-bottom: 18px;
  border-bottom: 2px solid var(--yc-ink);
  letter-spacing: 0.04em;
}

.chapter-content {
  line-height: 1.95;
  color: var(--yc-text);
  font-size: 17px;
  text-align: justify;
}

.rich-content :deep(p) {
  margin: 14px 0;
  text-align: justify;
}

.rich-content :deep(h1),
.rich-content :deep(h2),
.rich-content :deep(h3) {
  font-family: var(--yc-font-display);
  color: var(--yc-text);
}

.rich-content :deep(h1) {
  font-size: 24px;
  margin: 28px 0 16px;
  padding-bottom: 10px;
  border-bottom: 1px solid var(--yc-line);
}

.rich-content :deep(h2) {
  font-size: 22px;
  margin: 24px 0 14px;
}

.rich-content :deep(h3) {
  font-size: 18px;
  margin: 20px 0 12px;
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
  color: var(--yc-text);
  font-weight: 600;
}

.rich-content :deep(em) {
  font-style: italic;
  color: var(--yc-ink-soft);
}

.rich-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 6px;
  margin: 20px 0;
}

.rich-content :deep(blockquote) {
  border-left: 3px solid var(--yc-ink);
  padding: 12px 18px;
  margin: 20px 0;
  background: rgba(22, 58, 60, 0.05);
  border-radius: 4px;
  color: var(--yc-ink-soft);
  font-style: italic;
  font-size: 15px;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s ease-in;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media (max-width: 992px) {
  .reader-container {
    flex-direction: column;
  }

  .reader-sidebar {
    width: 100%;
    height: auto;
    position: relative;
    top: 0;
    max-height: 320px;
  }

  .reader-main {
    min-height: auto;
    padding: 28px 20px;
  }
}

@media (max-width: 768px) {
  .reader-header {
    padding: 12px 16px;
  }

  .book-title-header h2 {
    font-size: 15px;
  }

  .reader-container {
    padding: 16px;
  }

  .chapter-title {
    font-size: 22px;
  }

  .chapter-content {
    font-size: 16px;
  }
}
</style>
