<template>
  <div class="tiptap-toolbar">
    <div class="toolbar-group">
      <el-button-group>
        <el-button
          size="small"
          :type="editor.isActive('bold') ? 'primary' : ''"
          @click="editor.chain().focus().toggleBold().run()"
          title="加粗 (Ctrl+B)"
        >
          <strong>B</strong>
        </el-button>
        <el-button
          size="small"
          :type="editor.isActive('italic') ? 'primary' : ''"
          @click="editor.chain().focus().toggleItalic().run()"
          title="斜体 (Ctrl+I)"
        >
          <em>I</em>
        </el-button>
        <el-button
          size="small"
          :type="editor.isActive('underline') ? 'primary' : ''"
          @click="editor.chain().focus().toggleUnderline().run()"
          title="下划线 (Ctrl+U)"
        >
          <u>U</u>
        </el-button>
        <el-button
          size="small"
          :type="editor.isActive('strike') ? 'primary' : ''"
          @click="editor.chain().focus().toggleStrike().run()"
          title="删除线"
        >
          <s>S</s>
        </el-button>
      </el-button-group>
    </div>

    <el-divider direction="vertical" />

    <div class="toolbar-group">
      <el-select
        v-model="headingLevel"
        size="small"
        style="width: 120px"
        placeholder="标题"
        @change="setHeading"
      >
        <el-option label="正文" value="paragraph" />
        <el-option label="标题 1" value="1" />
        <el-option label="标题 2" value="2" />
        <el-option label="标题 3" value="3" />
        <el-option label="标题 4" value="4" />
      </el-select>
    </div>

    <el-divider direction="vertical" />

    <div class="toolbar-group">
      <el-button-group>
        <el-button
          size="small"
          :type="editor.isActive('bulletList') ? 'primary' : ''"
          @click="editor.chain().focus().toggleBulletList().run()"
          title="无序列表"
        >
          <el-icon><List /></el-icon>
        </el-button>
        <el-button
          size="small"
          :type="editor.isActive('orderedList') ? 'primary' : ''"
          @click="editor.chain().focus().toggleOrderedList().run()"
          title="有序列表"
        >
          <el-icon><Sort /></el-icon>
        </el-button>
        <el-button
          size="small"
          :type="editor.isActive('blockquote') ? 'primary' : ''"
          @click="editor.chain().focus().toggleBlockquote().run()"
          title="引用"
        >
          <el-icon><ChatLineSquare /></el-icon>
        </el-button>
        <el-button
          size="small"
          :type="editor.isActive('codeBlock') ? 'primary' : ''"
          @click="editor.chain().focus().toggleCodeBlock().run()"
          title="代码块"
        >
          <el-icon><Document /></el-icon>
        </el-button>
      </el-button-group>
    </div>

    <el-divider direction="vertical" />

    <div class="toolbar-group">
      <el-button-group>
        <el-button
          size="small"
          @click="setLink"
          :type="editor.isActive('link') ? 'primary' : ''"
          title="链接"
        >
          <el-icon><Link /></el-icon>
        </el-button>
        <el-button
          size="small"
          @click="addImage"
          title="插入图片"
        >
          <el-icon><Picture /></el-icon>
        </el-button>
        <el-button
          size="small"
          @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()"
          title="插入表格"
        >
          <el-icon><Grid /></el-icon>
        </el-button>
        <el-button
          size="small"
          @click="editor.chain().focus().setHorizontalRule().run()"
          title="分隔线"
        >
          <el-icon><Minus /></el-icon>
        </el-button>
      </el-button-group>
    </div>

    <el-divider direction="vertical" />

    <div class="toolbar-group">
      <el-button-group>
        <el-button
          size="small"
          @click="editor.chain().focus().undo().run()"
          :disabled="!editor.can().undo()"
          title="撤销 (Ctrl+Z)"
        >
          <el-icon><RefreshLeft /></el-icon>
        </el-button>
        <el-button
          size="small"
          @click="editor.chain().focus().redo().run()"
          :disabled="!editor.can().redo()"
          title="重做 (Ctrl+Y)"
        >
          <el-icon><RefreshRight /></el-icon>
        </el-button>
      </el-button-group>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { ElMessageBox } from 'element-plus'
import { List, Sort, ChatLineSquare, Document, Link, Picture, Grid, Minus, RefreshLeft, RefreshRight } from '@element-plus/icons-vue'

const props = defineProps({
  editor: {
    type: Object,
    required: true,
  },
})

const headingLevel = ref('paragraph')

watch(() => props.editor?.getAttributes('heading'), (attrs) => {
  if (attrs.level) {
    headingLevel.value = String(attrs.level)
  } else {
    headingLevel.value = 'paragraph'
  }
}, { immediate: true })

const setHeading = () => {
  if (headingLevel.value === 'paragraph') {
    props.editor.chain().focus().setParagraph().run()
  } else {
    props.editor.chain().focus().toggleHeading({ level: parseInt(headingLevel.value) }).run()
  }
}

const setLink = async () => {
  const previousUrl = props.editor.getAttributes('link').href
  const url = await ElMessageBox.prompt('请输入链接地址', '插入链接', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    inputValue: previousUrl,
    inputPlaceholder: 'https://example.com',
  }).catch(() => null)

  if (url === null) {
    return
  }

  if (url.value === '') {
    props.editor.chain().focus().extendMarkRange('link').unsetLink().run()
    return
  }

  props.editor.chain().focus().extendMarkRange('link').setLink({ href: url.value }).run()
}

const addImage = async () => {
  const url = await ElMessageBox.prompt('请输入图片地址', '插入图片', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    inputPlaceholder: 'https://example.com/image.jpg',
  }).catch(() => null)

  if (url && url.value) {
    props.editor.chain().focus().setImage({ src: url.value }).run()
  }
}
</script>

<style scoped>
.tiptap-toolbar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: #fafbfc;
  border-bottom: 1px solid #e4e7ed;
  flex-wrap: wrap;
  min-height: 50px;
}

.toolbar-group {
  display: flex;
  align-items: center;
  gap: 4px;
}

:deep(.el-button-group .el-button) {
  margin-left: 0;
}
</style>
