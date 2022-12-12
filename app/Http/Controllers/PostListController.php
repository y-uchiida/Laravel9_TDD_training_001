<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostListController extends Controller
{
    public function index()
    {
        $posts = Post::quesry()
            ->with('user') // N+1対策
            ->orderByDesc('comments_count')
            ->withCount('comments')
            ->get();

        return view('index', compact('posts'));
    }
}