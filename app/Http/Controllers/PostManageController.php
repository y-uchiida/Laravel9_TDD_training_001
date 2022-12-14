<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostManageController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->posts;
        return view('mypage.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('mypage.posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:255'],
            'body' => ['required'],
            'status' => ['nullable', 'boolean'],
        ]);

        $post = auth()->user()->posts()->create([
            ...$request->post(),
            $request->boolean('is_published')
        ]);
        return redirect()->route('mypage:edit', ['id' => $post->id]);
    }
}