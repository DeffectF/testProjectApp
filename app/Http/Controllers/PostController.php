<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\StatisticsService;

class PostController extends Controller
{
    protected StatisticsService $statsService;

    public function __construct(StatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function stats(): \Illuminate\Http\JsonResponse
    {
        $postCount = $this->statsService->getPostCount();
        return response()->json(['post_count' => $postCount], 200);
    }
    public function index(): PostCollection
    {
        $posts = Post::with(['author', 'category'])->paginate();
        return new PostCollection($posts);
    }

    public function store(StorePostRequest $request): PostResource
    {
        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'category_id' => $request->input('category_id')
        ]);

        return new PostResource($post);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post->loadMissing(['author', 'category']));
    }
}
