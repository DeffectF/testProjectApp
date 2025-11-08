<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/popular-posts', function () {
    $posts = Post::published()->popular()->get();
    return view('posts.popular', compact('posts'));
});

