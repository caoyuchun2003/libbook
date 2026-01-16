<template>
  <div class="role-management">
    <div class="page-header">
      <h2>角色管理</h2>
    </div>

    <!-- 角色统计 -->
    <el-row :gutter="20" style="margin-bottom: 20px">
      <el-col :span="8">
        <el-card>
          <div class="stat-item">
            <div class="stat-icon" style="background: #409eff;">
              <el-icon size="30"><User /></el-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ statistics.total || 0 }}</div>
              <div class="stat-label">总用户数</div>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card>
          <div class="stat-item">
            <div class="stat-icon" style="background: #f56c6c;">
              <el-icon size="30"><UserFilled /></el-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ statistics.admin || 0 }}</div>
              <div class="stat-label">管理员</div>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card>
          <div class="stat-item">
            <div class="stat-icon" style="background: #67c23a;">
              <el-icon size="30"><Avatar /></el-icon>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ statistics.user || 0 }}</div>
              <div class="stat-label">普通用户</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 角色标签页 -->
    <el-card>
      <el-tabs v-model="activeRole" @tab-change="handleRoleChange">
        <el-tab-pane label="管理员" name="admin">
          <template #label>
            <span>管理员 <el-badge :value="statistics.admin" class="item" /></span>
          </template>
          <div class="table-toolbar">
            <el-input
              v-model="searchKeyword"
              placeholder="搜索姓名或邮箱"
              style="width: 300px"
              clearable
              @clear="loadUsers"
              @keyup.enter="loadUsers"
            >
              <template #prefix>
                <el-icon><Search /></el-icon>
              </template>
            </el-input>
            <el-button type="primary" @click="loadUsers">
              <el-icon><Refresh /></el-icon>
              刷新
            </el-button>
            <el-button 
              type="danger" 
              :disabled="selectedUsers.length === 0"
              @click="batchUpdateRole('user')"
            >
              批量设为普通用户
            </el-button>
          </div>
          <el-table
            :data="users"
            style="width: 100%"
            v-loading="loading"
            @selection-change="handleSelectionChange"
          >
            <el-table-column type="selection" width="55" />
            <el-table-column prop="id" label="ID" width="80" />
            <el-table-column prop="name" label="姓名" />
            <el-table-column prop="email" label="邮箱" />
            <el-table-column prop="role" label="角色">
              <template #default="scope">
                <el-tag type="danger">管理员</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="created_at" label="注册时间" />
            <el-table-column label="操作" width="150">
              <template #default="scope">
                <el-button
                  size="small"
                  type="warning"
                  @click="updateUserRole(scope.row.id, 'user')"
                  :disabled="scope.row.id === currentUserId"
                >
                  设为普通用户
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
        </el-tab-pane>

        <el-tab-pane label="普通用户" name="user">
          <template #label>
            <span>普通用户 <el-badge :value="statistics.user" class="item" /></span>
          </template>
          <div class="table-toolbar">
            <el-input
              v-model="searchKeyword"
              placeholder="搜索姓名或邮箱"
              style="width: 300px"
              clearable
              @clear="loadUsers"
              @keyup.enter="loadUsers"
            >
              <template #prefix>
                <el-icon><Search /></el-icon>
              </template>
            </el-input>
            <el-button type="primary" @click="loadUsers">
              <el-icon><Refresh /></el-icon>
              刷新
            </el-button>
            <el-button 
              type="danger" 
              :disabled="selectedUsers.length === 0"
              @click="batchUpdateRole('admin')"
            >
              批量设为管理员
            </el-button>
          </div>
          <el-table
            :data="users"
            style="width: 100%"
            v-loading="loading"
            @selection-change="handleSelectionChange"
          >
            <el-table-column type="selection" width="55" />
            <el-table-column prop="id" label="ID" width="80" />
            <el-table-column prop="name" label="姓名" />
            <el-table-column prop="email" label="邮箱" />
            <el-table-column prop="role" label="角色">
              <template #default="scope">
                <el-tag type="primary">普通用户</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="created_at" label="注册时间" />
            <el-table-column label="操作" width="150">
              <template #default="scope">
                <el-button
                  size="small"
                  type="success"
                  @click="updateUserRole(scope.row.id, 'admin')"
                >
                  设为管理员
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
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { rolesApi } from '@/api/roles'
import { useAuthStore } from '@/store/auth'
import { ElMessage, ElMessageBox } from 'element-plus'
import { User, UserFilled, Avatar, Search, Refresh } from '@element-plus/icons-vue'

const authStore = useAuthStore()
const currentUserId = authStore.user?.id

const loading = ref(false)
const statistics = ref({
  total: 0,
  admin: 0,
  user: 0,
})
const activeRole = ref('admin')
const users = ref([])
const selectedUsers = ref([])
const searchKeyword = ref('')
const currentPage = ref(1)
const pageSize = ref(15)
const total = ref(0)

onMounted(() => {
  loadStatistics()
  loadUsers()
})

const loadStatistics = async () => {
  try {
    const res = await rolesApi.getStatistics()
    statistics.value = res.data
  } catch (error) {
    ElMessage.error('加载统计数据失败')
  }
}

const loadUsers = async () => {
  loading.value = true
  try {
    const res = await rolesApi.getUsersByRole(activeRole.value, {
      page: currentPage.value,
      per_page: pageSize.value,
      search: searchKeyword.value,
    })
    users.value = res.data.data || []
    total.value = res.data.total || 0
  } catch (error) {
    ElMessage.error('加载用户列表失败')
  } finally {
    loading.value = false
  }
}

const handleRoleChange = () => {
  currentPage.value = 1
  searchKeyword.value = ''
  selectedUsers.value = []
  loadUsers()
}

const handleSelectionChange = (selection) => {
  selectedUsers.value = selection.map(u => u.id)
}

const updateUserRole = async (userId, newRole) => {
  try {
    await ElMessageBox.confirm(
      `确定要将该用户${newRole === 'admin' ? '设为管理员' : '设为普通用户'}吗？`,
      '提示',
      {
        type: 'warning',
      }
    )
    
    await rolesApi.updateUserRole(userId, newRole)
    ElMessage.success('角色更新成功')
    loadStatistics()
    loadUsers()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error.response?.data?.message || '角色更新失败')
    }
  }
}

const batchUpdateRole = async (newRole) => {
  if (selectedUsers.value.length === 0) {
    ElMessage.warning('请先选择要操作的用户')
    return
  }

  try {
    await ElMessageBox.confirm(
      `确定要将选中的 ${selectedUsers.value.length} 个用户${newRole === 'admin' ? '设为管理员' : '设为普通用户'}吗？`,
      '批量操作',
      {
        type: 'warning',
      }
    )
    
    await rolesApi.batchUpdateRole(selectedUsers.value, newRole)
    ElMessage.success('批量更新成功')
    selectedUsers.value = []
    loadStatistics()
    loadUsers()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error.response?.data?.message || '批量更新失败')
    }
  }
}
</script>

<style scoped>
.role-management {
  padding: 20px;
}

.page-header {
  margin-bottom: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 15px;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 28px;
  font-weight: bold;
  color: #303133;
}

.stat-label {
  margin-top: 5px;
  color: #909399;
  font-size: 14px;
}

.table-toolbar {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  align-items: center;
}

.item {
  margin-left: 8px;
}
</style>
