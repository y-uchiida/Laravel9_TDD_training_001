<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return redirect()->route('mypage:edit', ['post' => $post->id]);
    }

    public function edit(Post $post)
    {
        if (auth()->user()->isNot($post->user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        // old() に入力内容が保持されていればそれを使用し、そうでなければ$post を使用
        $data = old() ? old() : $post;

        return view('mypage.posts.edit', compact('data'));
    }

    public function update(Post $post, Request $request)
    {
        if (auth()->user()->isNot($post->user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $post->update([
            ...$request->post(),
            'is_published' => $request->boolean('is_published')
        ]);
        return redirect(route('mypage:edit', ['post' => $post->id]));
    }

    public function destroy(Post $post)
    {
        // TODO: 所有権チェック
        if (auth()->user()->isNot($post->user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $post->delete();

        return redirect()->route('mypage:posts');
    }
}