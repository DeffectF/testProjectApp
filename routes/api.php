<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiVersionMiddleware;
use App\Http\Middleware\DynamicCors;
use App\Http\Middleware\RequestResponseLogger;
use App\Http\Middleware\ThrottleRequestsByIpMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('posts', 'PostController')->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::apiResource('comments', 'CommentController')->only(['index', 'show', 'store', 'update', 'destroy']);
});

Route::middleware('ensure.token.is.valid')->post('/posts/store', [PostController::class, 'store'])->name('posts.store');

$routeMiddleware = [
    'throttle.ip' => ThrottleRequestsByIpMiddleware::class,
    DynamicCors::class,
    RequestResponseLogger::class,
    ApiVersionMiddleware::class,
];

Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => ['throttle.ip']], function () {
        Route::get('/v1/users', [UserController::class, 'index']);
        Route::get('/v2/posts', [PostController::class, 'list']);
    });
});


