<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => '未认证'], 401);
        }

        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => '无权限访问'], 403);
        }

        return $next($request);
    }
}
