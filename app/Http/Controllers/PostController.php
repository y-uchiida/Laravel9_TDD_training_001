<?php

namespace App\Http\Controllers;

use App\Utils\SampleClass;
use App\Models\Post;
use Brick\Math\BigInteger;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->onlyOpen()
            ->with('user') // N+1対策
            ->orderByDesc('comments_count')
            ->withCount('comments')
            ->get();

        return view('index', compact('posts'));
    }

    public function show(Post $post, SampleClass $sampleClass)
    {
        if (!$post->is_published) {
            abort(403);
        }
        $random = $sampleClass->randomStr(10);
        return view('post.show', compact('post', 'random'));
    }
}