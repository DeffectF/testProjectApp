<?php

namespace App\Services;

use App\Models\Post;

class StatisticsService
{
    public function getPostCount(): int
    {
        return Post::count();
    }
}
