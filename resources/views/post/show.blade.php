@extends('layouts.index')

@section('content')
    @if (today()->is('12-25'))
        <div>メリークリスマス！</div>
    @endif
    <h1>{{ $post->title }}</h1>
    <div>
        {!! nl2br(e($post->body)) !!}
    </div>
    <p>author: {{ $post->user->name }}</p>

    <h2>コメント</h2>
    @foreach ($post->comments()->oldest()->get() as $comment)
        <hr>
        <p>{{ $comment->name }} ({{ $comment->created_at }})</p>
        <p>{!! nl2br(e($comment->body)) !!}</p>
    @endforeach
@endsection
