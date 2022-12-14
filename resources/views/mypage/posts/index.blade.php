@extends('layouts.index')
@section('content')
    <h1>マイブログ一覧</h1>
    <a href="/mypage/posts/create">ブログ新規登録</a>
    <hr>

    @include('inc.status')
    <table>
        <tr>
            <th>ブログ名</th>
        </tr>

        @foreach ($posts as $post)
            <tr>
                <td>
                    <a href="{{ route('mypage:edit', $post) }}">{{ $post->title }}</a>
                </td>
                <td>
                    <form method="post" action="{{ route('mypage:delete', $post) }}">
                        @csrf @method('delete') <input type="submit" value="削除">
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
