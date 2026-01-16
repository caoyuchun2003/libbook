<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookChapterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 公开路由
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// 需要认证的路由
Route::middleware('auth:sanctum')->group(function () {
    // 用户相关
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 图书管理
    Route::apiResource('books', BookController::class);
    Route::get('/books/search/{keyword}', [BookController::class, 'search']);
    
    // 图书章节管理
    Route::prefix('books/{book}/chapters')->group(function () {
        Route::get('/', [BookChapterController::class, 'index']);
        Route::post('/', [BookChapterController::class, 'store']);
        Route::put('/order', [BookChapterController::class, 'updateOrder']);
    });
    Route::apiResource('chapters', BookChapterController::class)->except(['index', 'store']);
    
    // 分类管理
    Route::apiResource('categories', CategoryController::class);
    
    // 用户管理（仅管理员）
    Route::middleware('admin')->group(function () {
        Route::apiResource('users', UserController::class);
        
        // 角色管理
        Route::prefix('roles')->group(function () {
            Route::get('/statistics', [RoleController::class, 'statistics']);
            Route::get('/{role}/users', [RoleController::class, 'getUsersByRole']);
            Route::post('/batch-update', [RoleController::class, 'updateUsersRole']);
            Route::put('/users/{userId}', [RoleController::class, 'updateUserRole']);
        });
    });
});

// 文档查看（公开访问）
Route::get('/docs', [DocsController::class, 'listDocs']);
Route::get('/docs/{filename}', [DocsController::class, 'getDoc']);
