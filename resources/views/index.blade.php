@extends('layouts.index')

@section('content')
    <h1>ブログ一覧</h1>
    <ul>
        @foreach ($posts as $index => $post)
            <li>{{ $post->title }}(author: {{ $post->user->name }})</li>
        @endforeach
    </ul>
@endsection
