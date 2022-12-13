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
@endsection
