<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('posts', 'PostController')->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::apiResource('comments', 'CommentController')->only(['index', 'show', 'store', 'update', 'destroy']);
});

Route::middleware('ensure.token.is.valid')->post('/posts/store', [PostController::class, 'store'])->name('posts.store');

