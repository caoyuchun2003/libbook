<template>
  <div class="auth-page">
    <div class="auth-brand">
      <div class="brand-inner">
        <svg class="brand-mark" viewBox="0 0 64 64" aria-hidden="true">
          <rect width="64" height="64" rx="12" fill="#f7faf9" opacity=".12"/>
          <path d="M18 16h20c4 0 7 3 7 7v25H25c-4 0-7-3-7-7V16z" fill="#f7faf9" opacity=".92"/>
          <path d="M18 16v25c0 4 3 7 7 7h20" stroke="#c4783a" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        <h1 class="brand-name">宇春书城</h1>
        <p class="brand-tagline">读好书，管好书</p>
      </div>
      <div class="brand-texture" aria-hidden="true" />
    </div>

    <div class="auth-panel">
      <div class="auth-card">
        <h2 class="auth-title">注册</h2>
        <p class="auth-subtitle">创建账号，开始收藏与阅读</p>

        <el-form :model="form" :rules="rules" ref="formRef" class="auth-form" @submit.prevent="handleRegister">
          <el-form-item prop="name">
            <el-input v-model="form.name" size="large" placeholder="姓名" />
          </el-form-item>
          <el-form-item prop="email">
            <el-input v-model="form.email" type="email" size="large" placeholder="邮箱" />
          </el-form-item>
          <el-form-item prop="password">
            <el-input v-model="form.password" type="password" size="large" placeholder="密码（至少 8 位）" show-password />
          </el-form-item>
          <el-form-item prop="password_confirmation">
            <el-input v-model="form.password_confirmation" type="password" size="large" placeholder="确认密码" show-password />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" size="large" class="submit-btn" :loading="loading" @click="handleRegister">
              注册
            </el-button>
          </el-form-item>
        </el-form>

        <p class="auth-switch">
          已有账号？
          <a href="#" @click.prevent="$router.push('/login')">立即登录</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { ElMessage } from 'element-plus'

const router = useRouter()
const authStore = useAuthStore()

const formRef = ref(null)
const loading = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const validatePass2 = (rule, value, callback) => {
  if (value === '') {
    callback(new Error('请再次输入密码'))
  } else if (value !== form.password) {
    callback(new Error('两次输入密码不一致'))
  } else {
    callback()
  }
}

const rules = {
  name: [{ required: true, message: '请输入姓名', trigger: 'blur' }],
  email: [
    { required: true, message: '请输入邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' },
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 8, message: '密码长度至少为8位', trigger: 'blur' },
  ],
  password_confirmation: [
    { required: true, validator: validatePass2, trigger: 'blur' },
  ],
}

const handleRegister = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        await authStore.register(form)
        ElMessage.success('注册成功')
        router.push('/')
      } catch (error) {
        ElMessage.error(error.response?.data?.message || '注册失败')
      } finally {
        loading.value = false
      }
    }
  })
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1.05fr 1fr;
}

.auth-brand {
  position: relative;
  background:
    radial-gradient(circle at 20% 20%, rgba(196, 120, 58, 0.18), transparent 40%),
    radial-gradient(circle at 80% 80%, rgba(247, 250, 249, 0.08), transparent 45%),
    var(--yc-ink);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.brand-texture {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
  background-size: 48px 48px;
  pointer-events: none;
}

.brand-inner {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: 40px;
}

.brand-mark {
  width: 72px;
  height: 72px;
  margin-bottom: 20px;
}

.brand-name {
  margin: 0;
  font-family: var(--yc-font-display);
  font-size: clamp(40px, 5vw, 56px);
  font-weight: 700;
  letter-spacing: 0.12em;
}

.brand-tagline {
  margin: 14px 0 0;
  font-size: 16px;
  color: rgba(247, 250, 249, 0.65);
  letter-spacing: 0.2em;
}

.auth-panel {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  background:
    radial-gradient(ellipse at bottom right, rgba(22, 58, 60, 0.06), transparent 50%),
    var(--yc-paper);
}

.auth-card {
  width: 100%;
  max-width: 400px;
}

.auth-title {
  margin: 0;
  font-size: 28px;
  color: var(--yc-text);
}

.auth-subtitle {
  margin: 8px 0 28px;
  color: var(--yc-muted);
  font-size: 14px;
}

.auth-form :deep(.el-form-item) {
  margin-bottom: 18px;
}

.submit-btn {
  width: 100%;
}

.auth-switch {
  margin: 8px 0 0;
  text-align: center;
  font-size: 14px;
  color: var(--yc-muted);
}

.auth-switch a {
  color: var(--yc-ink);
  font-weight: 600;
  text-decoration: none;
}

.auth-switch a:hover {
  color: var(--yc-accent);
}

@media (max-width: 860px) {
  .auth-page {
    grid-template-columns: 1fr;
  }

  .auth-brand {
    min-height: 200px;
  }

  .brand-name {
    font-size: 36px;
  }
}
</style>
