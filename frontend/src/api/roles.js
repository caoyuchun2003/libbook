import api from './axios'

export const rolesApi = {
  // 获取角色统计
  getStatistics() {
    return api.get('/roles/statistics')
  },
  
  // 获取指定角色的用户列表
  getUsersByRole(role, params = {}) {
    return api.get(`/roles/${role}/users`, { params })
  },
  
  // 批量更新用户角色
  batchUpdateRole(userIds, role) {
    return api.post('/roles/batch-update', {
      user_ids: userIds,
      role: role,
    })
  },
  
  // 更新单个用户角色
  updateUserRole(userId, role) {
    return api.put(`/roles/users/${userId}`, { role })
  },
}
