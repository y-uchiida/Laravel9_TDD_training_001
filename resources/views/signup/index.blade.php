@extends('layouts.index')

@section('content')
    <h1>ユーザー登録</h1>

    <form method="post">
        @csrf
        @include('inc.error')

        <div>
            <label>
                名前
                <input type="text" name="name" id="name" value="{{ old('name') }}">
            </label>
        </div>
        <div>
            <label>
                メールアドレス
                <input type="text" name="email" id="email" value="{{ old('email') }}">
            </label>
        </div>
        <div>
            <label>
                パスワード
                <input type="password" name="password" id="password" value="{{ old('password') }}">
            </label>
        </div>
        <input type="submit" value="送信する">
    </form>
@endsection
