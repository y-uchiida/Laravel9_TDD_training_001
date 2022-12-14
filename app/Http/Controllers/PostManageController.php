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
}