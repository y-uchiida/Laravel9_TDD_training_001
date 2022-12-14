<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserLoginController extends Controller
{
    public function index()
    {
        return view('mypage.login');
    }

    /**
     * 送信されたユーザー情報で認証を試行する
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // バリデーション通過出来たら、認証試行する
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // 認証成功時はマイページに遷移
            return redirect()->route('mypage:posts');
        }
        // 認証失敗時はログイン画面に遷移
        return redirect()->route('login')->withErrors(
            ['auth_fail' => 'メールアドレスかパスワードが間違っています']
        )->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect()->route('login')->with('status', 'ログアウトしました');
    }
}