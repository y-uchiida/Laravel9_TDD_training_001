@extends('layouts.index')

@section('content')
    <h1>ブログ一覧</h1>
    <ul>
        @foreach ($posts as $index => $post)
            <li>
                <a href="{{ route('post.show', ['post' => $post->id]) }}">{{ $post->title }}</a>
                (author: {{ $post->user->name }})
                ({{ $post->comments_count }} comments)
            </li>
        @endforeach
    </ul>
@endsection
