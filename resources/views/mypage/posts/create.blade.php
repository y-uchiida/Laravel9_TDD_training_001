@extends('layouts.index')

@section('content')
    <h1>マイブログ新規登録</h1>

    @include('inc.error')
    <form method="post">
        @csrf
        タイトル：<input type="text" name="title" style="width:400px" value="{{ old('title') }}">
        <br>
        本文：
        <textarea name="body" style="width:600px; height:200px;">{{ old('body') }}</textarea>
        <br>
        公開する：<label><input type="checkbox" name="is_published" value="1"
                {{ old('is_published') ? 'checked' : '' }}>公開する</label>
        <br><br>
        <input type="submit" value="送信する">
    </form>
@endsection
