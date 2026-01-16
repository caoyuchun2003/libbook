<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // API 请求返回 null，让中间件返回 JSON 响应
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }
        
        // Web 请求重定向到登录页（如果有定义）
        // 注意：如果 login 路由未定义，返回 null 避免错误
        try {
            return route('login', [], false);
        } catch (\Exception $e) {
            return '/login';
        }
    }
}
