<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * 获取角色统计
     */
    public function statistics()
    {
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();

        return response()->json([
            'total' => $totalUsers,
            'admin' => $adminCount,
            'user' => $userCount,
        ]);
    }

    /**
     * 获取指定角色的用户列表
     */
    public function getUsersByRole(Request $request, $role)
    {
        if (!in_array($role, ['admin', 'user'])) {
            return response()->json(['message' => '无效的角色'], 400);
        }

        $query = User::where('role', $role);

        // 搜索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    /**
     * 批量更新用户角色
     */
    public function updateUsersRole(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
            'role' => 'required|in:user,admin',
        ]);

        $userIds = $validated['user_ids'];
        $newRole = $validated['role'];

        // 防止将所有管理员都改为普通用户
        if ($newRole === 'user') {
            $adminCount = User::where('role', 'admin')->whereNotIn('id', $userIds)->count();
            if ($adminCount === 0) {
                return response()->json(['message' => '至少需要保留一个管理员账号'], 400);
            }
        }

        // 防止删除自己
        if (in_array(auth()->id(), $userIds) && $newRole === 'user') {
            return response()->json(['message' => '不能修改自己的角色'], 400);
        }

        User::whereIn('id', $userIds)->update(['role' => $newRole]);

        return response()->json([
            'message' => '角色更新成功',
            'updated_count' => count($userIds),
        ]);
    }

    /**
     * 更新单个用户角色
     */
    public function updateUserRole(Request $request, $userId)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user = User::findOrFail($userId);
        $newRole = $validated['role'];

        // 防止删除最后一个管理员
        if ($user->role === 'admin' && $newRole === 'user') {
            $adminCount = User::where('role', 'admin')->where('id', '!=', $userId)->count();
            if ($adminCount === 0) {
                return response()->json(['message' => '至少需要保留一个管理员账号'], 400);
            }
        }

        // 防止修改自己
        if ($user->id === auth()->id() && $newRole === 'user') {
            return response()->json(['message' => '不能修改自己的角色'], 400);
        }

        $user->update(['role' => $newRole]);

        return response()->json([
            'message' => '角色更新成功',
            'user' => $user,
        ]);
    }
}
