<template>
  <div class="book-detail-page" v-loading="loading">
    <el-button 
      @click="$router.back()" 
      :icon="ArrowLeft" 
      class="back-button"
      text
    >
      返回书城
    </el-button>
    
    <div v-if="book" class="book-content">
      <!-- 书籍封面和基本信息 -->
      <div class="book-hero">
        <div class="cover-container">
          <BookCover :book="book" image-class="book-cover-image" />
        </div>
        
        <div class="book-basic-info">
          <h1 class="book-title">{{ book.title }}</h1>
          <p v-if="book.description" class="book-subtitle">{{ book.description }}</p>
          <div class="book-author">
            <el-icon><User /></el-icon>
            <span>{{ book.author }}</span>
          </div>
          <div class="book-meta">
            <el-tag type="primary" effect="light" class="category-badge">
              {{ book.category?.name }}
            </el-tag>
            <span v-if="book.price" class="book-price">
              ¥{{ book.price }}
            </span>
          </div>
          <div class="book-extra-info">
            <div class="info-item">
              <span class="info-label">ISBN：</span>
              <span class="info-value">{{ book.isbn }}</span>
            </div>
            <div class="info-item" v-if="book.published_at">
              <span class="info-label">出版日期：</span>
              <span class="info-value">{{ book.published_at }}</span>
            </div>
          </div>
          <div class="book-actions">
            <el-button 
              type="primary" 
              size="large" 
              @click="startReading"
              class="start-reading-btn"
            >
              <el-icon><Reading /></el-icon>
              <span>开始阅读</span>
            </el-button>
          </div>
        </div>

        <!-- 作者简介（右侧） -->
        <div class="author-intro-sidebar" v-if="book.author_intro && book.author_intro.trim()">
          <div class="sidebar-title">
            <el-icon class="title-icon"><UserFilled /></el-icon>
            <span>作者简介</span>
          </div>
          <div class="sidebar-content" v-html="book.author_intro"></div>
        </div>
      </div>

      <!-- 目录和内容简介 - Tab 切换 -->
      <div class="content-block tabs-container">
        <el-tabs v-model="activeTab" type="border-card" class="custom-tabs">
          <!-- 目录 Tab -->
          <el-tab-pane label="目录" name="toc">
            <template #label>
              <span class="tab-label">
                <el-icon><List /></el-icon>
                <span>目录</span>
              </span>
            </template>
            <div class="toc-content" v-if="topLevelChapters && topLevelChapters.length > 0">
              <div 
                v-for="chapter in topLevelChapters" 
                :key="chapter.id" 
                class="toc-item"
              >
            <div 
              class="toc-title" 
              :class="{ 'has-children': chapter.children && chapter.children.length > 0 }"
            >
              <el-icon 
                v-if="chapter.children && chapter.children.length > 0" 
                class="expand-icon"
                :class="{ 'expanded': expandedChapters.has(chapter.id) }"
                @click.stop="toggleChapter(chapter.id)"
              >
                <ArrowRight />
              </el-icon>
              <span 
                class="toc-title-text" 
                @click="goToChapter(chapter.id)"
              >{{ chapter.title }}</span>
            </div>
                <transition name="slide-fade">
                  <div 
                    v-if="chapter.children && chapter.children.length > 0 && expandedChapters.has(chapter.id)" 
                    class="toc-children"
                  >
                    <div 
                      v-for="child in chapter.children" 
                      :key="child.id" 
                      class="toc-child"
                      @click.stop="goToChapter(child.id)"
                    >
                      <span class="toc-child-text">{{ child.title }}</span>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
            <el-empty v-else description="暂无目录" :image-size="100" />
          </el-tab-pane>

          <!-- 内容简介 Tab -->
          <el-tab-pane label="内容简介" name="intro">
            <template #label>
              <span class="tab-label">
                <el-icon><Document /></el-icon>
                <span>内容简介</span>
              </span>
            </template>
            <div v-if="book.content_intro && book.content_intro.trim()" class="intro-content">
              <div class="rich-content" v-html="book.content_intro"></div>
            </div>
            <el-empty v-else description="暂无内容简介" :image-size="100" />
          </el-tab-pane>
        </el-tabs>
      </div>


    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { booksApi } from '@/api/books'
import { chaptersApi } from '@/api/chapters'
import { ElMessage } from 'element-plus'
import { ArrowLeft, ArrowRight, Document, UserFilled, InfoFilled, User, List, Reading } from '@element-plus/icons-vue'
import BookCover from '@/components/BookCover.vue'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const book = ref(null)
const chapters = ref([])
const expandedChapters = ref(new Set())
const activeTab = ref('toc')

onMounted(() => {
  loadBook()
  loadChapters()
})

// 切换章节展开/折叠
const toggleChapter = (chapterId) => {
  if (expandedChapters.value.has(chapterId)) {
    expandedChapters.value.delete(chapterId)
  } else {
    expandedChapters.value.add(chapterId)
  }
}

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
    // 确保 id 和 parent_id 都是数字或 null
    const flatChapters = (res.data.data || res.data || []).map(ch => ({
      ...ch,
      id: parseInt(ch.id),
      parent_id: ch.parent_id ? parseInt(ch.parent_id) : null,
    })).sort((a, b) => a.order - b.order)
    
    // 构建树形结构
    chapters.value = buildChapterTree(flatChapters)
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

// 计算属性：获取顶级章节（用于显示）
const topLevelChapters = computed(() => {
  return chapters.value.filter(ch => ch.parent_id === null)
})

// 开始阅读
const startReading = () => {
  // 跳转到阅读页面，默认显示第一个章节
  router.push(`/books/${route.params.id}/read`)
}

// 跳转到指定章节
const goToChapter = (chapterId) => {
  router.push({
    path: `/books/${route.params.id}/read`,
    query: { chapterId }
  })
}

</script>

<style scoped>
.book-detail-page {
  padding: 8px 0 24px;
  max-width: 1000px;
  margin: 0 auto;
  min-height: calc(100vh - 160px);
}

.back-button {
  margin-bottom: 30px;
  color: var(--yc-ink-soft);
  font-size: 15px;
  padding: 8px 0;
  transition: all 0.3s;
}

.back-button:hover {
  color: var(--yc-ink);
  transform: translateX(-4px);
}

.book-content {
  background: var(--yc-surface);
  border-radius: 12px;
  padding: 36px;
  border: 1px solid var(--yc-line);
  box-shadow: none;
}

/* 书籍头部区域 */
.book-hero {
  display: grid;
  grid-template-columns: 200px 1fr auto;
  gap: 40px;
  margin-bottom: 50px;
  padding-bottom: 40px;
  border-bottom: 1px solid var(--yc-line);
  align-items: start;
}

.cover-container {
  flex-shrink: 0;
}

.book-cover-image {
  width: 200px;
  height: 280px;
  border-radius: 4px;
  box-shadow: 0 12px 28px rgba(22, 58, 60, 0.14);
  transition: transform 0.3s ease;
  cursor: pointer;
}

.book-cover-image:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 36px rgba(22, 58, 60, 0.18);
}

.cover-placeholder {
  width: 200px;
  height: 280px;
  background: linear-gradient(135deg, var(--yc-paper) 0%, #e8ebf0 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c0c4cc;
  border: 1px solid var(--yc-line);
}

.book-basic-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.book-title {
  font-family: var(--yc-font-display);
  font-size: 32px;
  font-weight: 700;
  color: var(--yc-text);
  margin: 0;
  line-height: 1.35;
  letter-spacing: 0.04em;
}

.book-subtitle {
  font-size: 16px;
  color: var(--yc-muted);
  margin: 12px 0 0 0;
  line-height: 1.6;
  font-weight: 400;
}

.book-author {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  color: var(--yc-ink-soft);
  margin-top: 8px;
}

.book-author .el-icon {
  color: var(--yc-muted);
}

.book-meta {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: 8px;
}

.category-badge {
  font-size: 14px;
  padding: 6px 14px;
  border-radius: 16px;
}

.book-price {
  font-size: 24px;
  font-weight: 700;
  color: var(--yc-accent);
}

.book-extra-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 16px;
  padding-top: 20px;
  border-top: 1px solid var(--yc-line);
}

.book-actions {
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid #f0f0f0;
}

.start-reading-btn {
  width: 100%;
  height: 48px;
  font-size: 16px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
}

.info-label {
  color: var(--yc-muted);
}

.info-value {
  color: var(--yc-ink-soft);
}

/* 作者简介侧边栏 */
.author-intro-sidebar {
  width: 280px;
  flex-shrink: 0;
  padding: 20px;
  background: #fafbfc;
  border-radius: 12px;
  border: 1px solid #f0f0f0;
}

.sidebar-title {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  font-size: 16px;
  font-weight: 600;
  color: var(--yc-text);
}

.sidebar-title .title-icon {
  font-size: 18px;
  color: var(--yc-ink);
}

.sidebar-content {
  line-height: 1.8;
  color: var(--yc-ink-soft);
  font-size: 14px;
}

.sidebar-content :deep(p) {
  margin: 10px 0;
}

.sidebar-content :deep(h1),
.sidebar-content :deep(h2),
.sidebar-content :deep(h3) {
  font-size: 16px;
  font-weight: 600;
  color: var(--yc-text);
  margin: 12px 0 8px 0;
}

.sidebar-content :deep(ul),
.sidebar-content :deep(ol) {
  margin: 10px 0;
  padding-left: 24px;
}

.sidebar-content :deep(li) {
  margin: 6px 0;
}

.sidebar-content :deep(strong) {
  color: var(--yc-text);
  font-weight: 600;
}

.sidebar-content :deep(em) {
  font-style: italic;
}

.sidebar-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 6px;
  margin: 12px 0;
}

.sidebar-content :deep(blockquote) {
  border-left: 3px solid var(--yc-ink);
  padding: 10px 14px;
  margin: 12px 0;
  background: rgba(22, 58, 60, 0.06);
  border-radius: 4px;
  font-size: 13px;
}

/* 内容区块 */
.content-block {
  margin-bottom: 40px;
}

.content-block:last-child {
  margin-bottom: 0;
}

.block-title {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  font-size: 20px;
  font-weight: 600;
  color: var(--yc-text);
}

/* Tab 容器样式 */
.tabs-container {
  margin-top: 40px;
}

.custom-tabs {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
}

.custom-tabs :deep(.el-tabs__header) {
  margin: 0;
  background: #fafbfc;
  border-bottom: 1px solid var(--yc-line);
}

.custom-tabs :deep(.el-tabs__nav-wrap) {
  padding: 0 20px;
}

.custom-tabs :deep(.el-tabs__item) {
  height: 56px;
  line-height: 56px;
  font-size: 16px;
  font-weight: 500;
  color: var(--yc-ink-soft);
  padding: 0 24px;
  transition: all 0.3s;
}

.custom-tabs :deep(.el-tabs__item:hover) {
  color: var(--yc-ink);
}

.custom-tabs :deep(.el-tabs__item.is-active) {
  color: var(--yc-ink);
  font-weight: 600;
}

.custom-tabs :deep(.el-tabs__active-bar) {
  background-color: var(--yc-ink);
  height: 3px;
}

.custom-tabs :deep(.el-tabs__content) {
  padding: 30px;
  min-height: 300px;
}

.tab-label {
  display: flex;
  align-items: center;
  gap: 8px;
}

.tab-label .el-icon {
  font-size: 18px;
}

/* 内容简介区域 */
.intro-content {
  padding: 0;
}

.title-icon {
  font-size: 22px;
  color: var(--yc-ink);
}

.rich-content {
  line-height: 1.9;
  color: var(--yc-ink-soft);
  font-size: 16px;
  text-align: justify;
}

.rich-content :deep(p) {
  margin: 14px 0;
  text-align: justify;
}

.rich-content :deep(h1) {
  font-size: 22px;
  font-weight: 700;
  color: var(--yc-text);
  margin: 24px 0 16px 0;
  padding-bottom: 8px;
  border-bottom: 2px solid var(--yc-line);
}

.rich-content :deep(h2) {
  font-size: 20px;
  font-weight: 600;
  color: var(--yc-text);
  margin: 20px 0 14px 0;
}

.rich-content :deep(h3) {
  font-size: 18px;
  font-weight: 600;
  color: var(--yc-text);
  margin: 18px 0 12px 0;
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
  border-radius: 8px;
  margin: 20px 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.rich-content :deep(blockquote) {
  border-left: 3px solid var(--yc-ink);
  padding: 12px 18px;
  margin: 20px 0;
  background: rgba(22, 58, 60, 0.06);
  border-radius: 4px;
  color: var(--yc-ink-soft);
  font-style: italic;
  font-size: 15px;
}

.simple-text {
  color: var(--yc-ink-soft);
  font-size: 16px;
  line-height: 1.9;
  margin: 0;
  text-align: justify;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .book-detail-page {
    padding: 15px;
  }

  .book-content {
    padding: 25px 20px;
  }

  .book-hero {
    grid-template-columns: 1fr;
    gap: 25px;
    margin-bottom: 35px;
    padding-bottom: 30px;
  }

  .author-intro-sidebar {
    width: 100%;
    order: 3;
  }

  .cover-container {
    align-self: center;
  }

  .book-cover-image,
  .cover-placeholder {
    width: 160px;
    height: 224px;
  }

  .book-title {
    font-size: 26px;
    text-align: center;
  }

  .book-author {
    justify-content: center;
    font-size: 16px;
  }

  .book-meta {
    justify-content: center;
    flex-wrap: wrap;
  }

  .book-extra-info {
    align-items: center;
    text-align: center;
  }

  .block-title {
    font-size: 18px;
  }

  .rich-content {
    font-size: 15px;
  }

  .simple-text {
    font-size: 15px;
  }
}

/* 目录样式 */
.toc-content {
  padding: 0;
}

.toc-item {
  margin-bottom: 12px;
  padding: 12px;
  background: #fafbfc;
  border-radius: 8px;
  border-left: 3px solid var(--yc-ink);
}

.toc-title {
  font-size: 16px;
  font-weight: 600;
  color: var(--yc-text);
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.toc-title.has-children {
  cursor: pointer;
  user-select: none;
  transition: all 0.3s ease;
}

.toc-title.has-children:hover {
  color: var(--yc-ink);
  background-color: rgba(22, 58, 60, 0.06);
  padding: 8px;
  margin: -8px;
  border-radius: 6px;
}

.expand-icon {
  font-size: 14px;
  color: var(--yc-muted);
  transition: transform 0.3s ease;
  flex-shrink: 0;
}

.expand-icon.expanded {
  transform: rotate(90deg);
  color: var(--yc-ink);
}

.toc-title-text {
  flex: 1;
  cursor: pointer;
  transition: color 0.3s;
}

.toc-title-text:hover {
  color: var(--yc-ink);
}

.toc-child {
  cursor: pointer;
  transition: color 0.3s;
}

.toc-child:hover {
  color: var(--yc-ink);
}

.toc-child-text {
  display: block;
}

.toc-children {
  margin-top: 8px;
  padding-left: 28px;
  border-left: 2px solid var(--yc-line);
}

.toc-child {
  padding: 6px 0;
  font-size: 15px;
  color: var(--yc-ink-soft);
}

.toc-child-text {
  display: block;
}

/* 展开/折叠动画 */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s ease-in;
}

.slide-fade-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}


</style>
