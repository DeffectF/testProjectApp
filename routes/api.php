<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('posts', 'PostController')->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::apiResource('comments', 'CommentController')->only(['index', 'show', 'store', 'update', 'destroy']);
});

Route::middleware('ensure.token.is.valid')->post('/posts/store', [PostController::class, 'store'])->name('posts.store');

$routeMiddleware = [
    'throttle.ip' => \App\Http\Middleware\ThrottleRequestsByIpMiddleware::class,
    \App\Http\Middleware\DynamicCors::class,
];

Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => ['throttle.ip']], function () {
        Route::get('/v1/users', [\App\Http\Controllers\UserController::class, 'index']);
        Route::get('/v2/posts', [\App\Http\Controllers\PostController::class, 'list']);
    });
});


