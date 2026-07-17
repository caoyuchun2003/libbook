<template>
  <el-image
    :src="src"
    :fit="fit"
    :class="imageClass"
    :style="imageStyle"
    :preview-src-list="preview ? [src] : undefined"
    :lazy="lazy"
  >
    <template #error>
      <div class="cover-fallback">
        <span>{{ fallbackLabel }}</span>
      </div>
    </template>
  </el-image>
</template>

<script setup>
import { computed } from 'vue'
import { resolveBookCover } from '@/utils/bookCover'

const props = defineProps({
  book: { type: Object, required: true },
  fit: { type: String, default: 'cover' },
  preview: { type: Boolean, default: true },
  lazy: { type: Boolean, default: false },
  imageClass: { type: [String, Object, Array], default: '' },
  imageStyle: { type: [String, Object], default: '' },
})

const src = computed(() => resolveBookCover(props.book))
const fallbackLabel = computed(() => (props.book?.title || '书').charAt(0))
</script>

<style scoped>
.cover-fallback {
  width: 100%;
  height: 100%;
  min-height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #163a3c, #2a5558);
  color: #fff;
  font-size: 28px;
  font-family: Georgia, serif;
}
</style>
