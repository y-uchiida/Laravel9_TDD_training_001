@extends('layouts.index')

@section('content')
    <h1>マイブログ更新</h1>

    @include('inc.error')
    @include('inc.status')

    <form method="post">
        @csrf

        タイトル：<input type="text" name="title" style="width:400px" value="{{ data_get($data, 'title') }}">
        <br>
        本文：
        <textarea name="body" style="width:600px; height:200px;">{{ data_get($data, 'body') }}</textarea>
        <br>
        公開する：<label><input type="checkbox" name="is_published" {{ data_get($data, 'is_published') ? 'checked' : '' }}
                value="1">公開する</label>

        <input type="submit" value="更新する">

    </form>
@endsection
