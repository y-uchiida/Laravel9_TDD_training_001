<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>ブログ</title>
</head>

<body>

    <nav>
        <ul>
            <li><a href="/">トップページ(ブログ一覧)</a></li>
            <li><a href="{{ route('mypage:posts') }}">マイブログ一覧</a></li>
            @auth
                <li>
                    <form name='logout' method="post" action="{{ route('logout') }}">
                        @csrf
                        <input type="submit" value="ログアウト" />
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}">ログイン</a></li>
            @endauth
        </ul>
    </nav>

    @yield('content')

</body>

</html>
