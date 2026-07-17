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
        <h2 class="auth-title">登录</h2>
        <p class="auth-subtitle">进入你的书架与书城</p>

        <div class="quick-accounts">
          <p class="quick-label">演示账户</p>
          <div class="account-buttons">
            <button
              v-for="account in testAccounts"
              :key="account.email"
              type="button"
              class="account-chip"
              :class="{ active: selectedAccount === account.email }"
              @click="selectAccount(account)"
            >
              {{ account.name }}
            </button>
          </div>
        </div>

        <el-form :model="form" :rules="rules" ref="formRef" class="auth-form" @submit.prevent="handleLogin">
          <el-form-item prop="email">
            <el-input v-model="form.email" type="email" size="large" placeholder="邮箱" />
          </el-form-item>
          <el-form-item prop="password">
            <el-input v-model="form.password" type="password" size="large" placeholder="密码" show-password />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" size="large" class="submit-btn" :loading="loading" @click="handleLogin">
              登录
            </el-button>
          </el-form-item>
        </el-form>

        <p class="auth-switch">
          还没有账号？
          <a href="#" @click.prevent="$router.push('/register')">立即注册</a>
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

const testAccounts = [
  { name: '管理员', email: 'admin@libbook.com', password: 'admin123' },
  { name: '普通用户', email: 'user@libbook.com', password: 'user123' },
]

const defaultAccount = testAccounts.find((a) => a.name === '普通用户')

const selectedAccount = ref(defaultAccount.email)

const form = reactive({
  email: defaultAccount.email,
  password: defaultAccount.password,
})

const selectAccount = (account) => {
  form.email = account.email
  form.password = account.password
  selectedAccount.value = account.email
}

const rules = {
  email: [
    { required: true, message: '请输入邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' },
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
  ],
}

const handleLogin = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        await authStore.login(form)
        ElMessage.success('登录成功')
        router.push('/')
      } catch (error) {
        ElMessage.error(error.response?.data?.message || '登录失败')
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
  animation: brandIn 0.55s ease-out;
}

@keyframes brandIn {
  from { opacity: 0; }
  to { opacity: 1; }
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
  animation: rise 0.6s ease-out 0.1s both;
}

@keyframes rise {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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
  animation: rise 0.55s ease-out 0.15s both;
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

.quick-accounts {
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--yc-line);
}

.quick-label {
  margin: 0 0 10px;
  font-size: 12px;
  color: var(--yc-muted);
  letter-spacing: 0.06em;
}

.account-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.account-chip {
  border: 1px solid var(--yc-line);
  background: var(--yc-surface);
  color: var(--yc-muted);
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  transition: border-color 0.2s, color 0.2s, background 0.2s;
}

.account-chip:hover,
.account-chip.active {
  border-color: var(--yc-ink-soft);
  color: var(--yc-ink);
  background: #fff;
}

.auth-form :deep(.el-form-item) {
  margin-bottom: 18px;
}

.submit-btn {
  width: 100%;
  --el-button-bg-color: var(--yc-ink);
  --el-button-border-color: var(--yc-ink);
  --el-button-hover-bg-color: var(--yc-ink-soft);
  --el-button-hover-border-color: var(--yc-ink-soft);
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
    min-height: 220px;
  }

  .brand-name {
    font-size: 36px;
  }
}
</style>
