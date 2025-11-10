<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
        public function run()
    {
        Comment::truncate();
        Post::truncate();
        Category::truncate();
        User::truncate();

        Category::factory()->count(5)->create();

        User::factory()->count(10)
            ->has(Post::factory()->count(rand(1, 5))
                ->state(function (array $attributes, Post $post) {
                    return ['category_id' => Category::all()->random()->id];
                })
            )
            ->create()
            ->each(function ($user) {
                foreach ($user->posts as $post) {
                    Comment::factory()->count(rand(0, 15))->create([
                        'post_id' => $post->id,
                        'user_id' => User::all()->random()->id,
                    ]);
                }
            });
    }
}
