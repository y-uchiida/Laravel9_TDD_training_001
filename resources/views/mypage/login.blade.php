@extends('layouts.index')

@section('content')
    <h1>ログイン画面</h1>

    <form method="post">
        @csrf
        @include('inc.error')
        @include('inc.status')
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

    <p style="margin-top:30px;">
        <a href="/signup">新規ユーザー登録</a>
    </p>
@endsection
