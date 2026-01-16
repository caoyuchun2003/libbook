<template>
  <div class="user-management">
    <div class="page-header">
      <h2>用户管理</h2>
      <el-button type="primary" @click="handleShowAddDialog">
        <el-icon><Plus /></el-icon>
        添加用户
      </el-button>
    </div>

    <el-card>
      <el-table :data="users" style="width: 100%" v-loading="loading">
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="姓名" />
        <el-table-column prop="email" label="邮箱" />
        <el-table-column prop="role" label="角色">
          <template #default="scope">
            <el-tag :type="scope.row.role === 'admin' ? 'danger' : 'primary'">
              {{ scope.row.role === 'admin' ? '管理员' : '普通用户' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="注册时间" />
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <el-button size="small" @click="editUser(scope.row)">编辑</el-button>
            <el-button 
              size="small" 
              type="danger" 
              @click="deleteUser(scope.row.id)"
              :disabled="scope.row.role === 'admin'"
            >
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        v-model:current-page="currentPage"
        v-model:page-size="pageSize"
        :total="total"
        :page-sizes="[10, 15, 20, 50]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="loadUsers"
        @current-change="loadUsers"
        style="margin-top: 20px"
      />
    </el-card>

    <!-- 添加用户对话框 -->
    <el-dialog v-model="showAddDialog" title="添加用户" width="500px" @close="resetAddForm">
      <el-form :model="addForm" :rules="addFormRules" ref="addFormRef" label-width="100px">
        <el-form-item label="姓名" prop="name">
          <el-input v-model="addForm.name" placeholder="请输入姓名" />
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="addForm.email" type="email" placeholder="请输入邮箱" />
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model="addForm.password" type="password" placeholder="请输入密码（至少8位）" show-password />
        </el-form-item>
        <el-form-item label="确认密码" prop="password_confirmation">
          <el-input v-model="addForm.password_confirmation" type="password" placeholder="请再次输入密码" show-password />
        </el-form-item>
        <el-form-item label="角色" prop="role">
          <el-select v-model="addForm.role" style="width: 100%">
            <el-option label="普通用户" value="user" />
            <el-option label="管理员" value="admin" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showAddDialog = false">取消</el-button>
        <el-button type="primary" @click="createUser" :loading="addLoading">创建</el-button>
      </template>
    </el-dialog>

    <!-- 编辑用户对话框 -->
    <el-dialog v-model="showDialog" title="编辑用户" width="500px">
      <el-form :model="userForm" label-width="100px">
        <el-form-item label="姓名">
          <el-input v-model="userForm.name" />
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="userForm.email" disabled />
        </el-form-item>
        <el-form-item label="角色">
          <el-select v-model="userForm.role" style="width: 100%">
            <el-option label="普通用户" value="user" />
            <el-option label="管理员" value="admin" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showDialog = false">取消</el-button>
        <el-button type="primary" @click="updateUser">保存</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usersApi } from '@/api/users'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'

const loading = ref(false)
const users = ref([])
const currentPage = ref(1)
const pageSize = ref(15)
const total = ref(0)
const showDialog = ref(false)
const showAddDialog = ref(false)
const addLoading = ref(false)
const userForm = ref({})
const addFormRef = ref(null)
const addForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'user',
})

// 添加用户表单验证规则
const validatePasswordConfirm = (rule, value, callback) => {
  if (value !== addForm.value.password) {
    callback(new Error('两次输入的密码不一致'))
  } else {
    callback()
  }
}

const addFormRules = {
  name: [
    { required: true, message: '请输入姓名', trigger: 'blur' },
  ],
  email: [
    { required: true, message: '请输入邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' },
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 8, message: '密码长度至少为8位', trigger: 'blur' },
  ],
  password_confirmation: [
    { required: true, message: '请再次输入密码', trigger: 'blur' },
    { validator: validatePasswordConfirm, trigger: 'blur' },
  ],
  role: [
    { required: true, message: '请选择角色', trigger: 'change' },
  ],
}

onMounted(() => {
  loadUsers()
})

const loadUsers = async () => {
  loading.value = true
  try {
    // 注意：需要后端提供用户列表 API
    // 这里先使用模拟数据，实际需要调用 API
    const res = await usersApi.getUsers({
      page: currentPage.value,
      per_page: pageSize.value,
    })
    users.value = res.data.data || []
    total.value = res.data.total || 0
  } catch (error) {
    // 如果 API 不存在，显示提示
    ElMessage.warning('用户管理 API 尚未实现，请先在后端添加用户管理接口')
    users.value = []
  } finally {
    loading.value = false
  }
}

const handleShowAddDialog = () => {
  showAddDialog.value = true
}

const resetAddForm = () => {
  addForm.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
  }
  if (addFormRef.value) {
    addFormRef.value.clearValidate()
  }
}

const createUser = async () => {
  if (!addFormRef.value) return
  
  await addFormRef.value.validate(async (valid) => {
    if (valid) {
      addLoading.value = true
      try {
        await usersApi.createUser({
          name: addForm.value.name,
          email: addForm.value.email,
          password: addForm.value.password,
          role: addForm.value.role,
        })
        ElMessage.success('用户创建成功')
        showAddDialog.value = false
        resetAddForm()
        loadUsers()
      } catch (error) {
        ElMessage.error(error.response?.data?.message || '创建用户失败')
      } finally {
        addLoading.value = false
      }
    }
  })
}

const editUser = (user) => {
  userForm.value = { ...user }
  showDialog.value = true
}

const updateUser = async () => {
  try {
    await usersApi.updateUser(userForm.value.id, {
      name: userForm.value.name,
      role: userForm.value.role,
    })
    ElMessage.success('更新成功')
    showDialog.value = false
    loadUsers()
  } catch (error) {
    ElMessage.error('更新失败')
  }
}

const deleteUser = async (id) => {
  try {
    await ElMessageBox.confirm('确定要删除该用户吗？', '提示', {
      type: 'warning',
    })
    await usersApi.deleteUser(id)
    ElMessage.success('删除成功')
    loadUsers()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}
</script>

<style scoped>
.user-management {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
</style>
