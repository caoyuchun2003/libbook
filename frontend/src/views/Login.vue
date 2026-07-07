<template>
  <div class="login-container">
    <el-card class="login-card">
      <template #header>
        <h2>登录</h2>
      </template>
      
      <!-- 快速选择账户 -->
      <div class="quick-accounts">
        <el-text type="info" size="small">快速选择测试账户：</el-text>
        <div class="account-buttons">
          <el-button 
            v-for="account in testAccounts" 
            :key="account.email"
            size="small"
            :type="selectedAccount === account.email ? 'primary' : 'default'"
            @click="selectAccount(account)"
          >
            {{ account.name }}
          </el-button>
        </div>
      </div>

      <el-divider />

      <el-form :model="form" :rules="rules" ref="formRef" @submit.prevent="handleLogin">
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="form.email" type="email" placeholder="请输入邮箱" />
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model="form.password" type="password" placeholder="请输入密码" show-password />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleLogin" :loading="loading" style="width: 100%">
            登录
          </el-button>
        </el-form-item>
        <el-form-item>
          <el-link type="primary" @click="$router.push('/register')">
            还没有账号？立即注册
          </el-link>
        </el-form-item>
      </el-form>
    </el-card>
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
const selectedAccount = ref('')

// 测试账户列表
const testAccounts = [
  {
    name: '管理员',
    email: 'admin@libbook.com',
    password: 'admin123',
    role: 'admin',
  },
  {
    name: '普通用户',
    email: 'user@libbook.com',
    password: 'user123',
    role: 'user',
  },
]

const form = reactive({
  email: '',
  password: '',
})

// 选择账户
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
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
  width: 450px;
}

.quick-accounts {
  margin-bottom: 10px;
}

.account-buttons {
  display: flex;
  gap: 10px;
  margin-top: 10px;
  flex-wrap: wrap;
}

.account-buttons .el-button {
  flex: 1;
  min-width: 100px;
}
</style>
