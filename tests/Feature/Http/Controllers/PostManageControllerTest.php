<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostManageControllerTest extends TestCase
{
    /** @test */
    public function ユーザー認証済みでなければ、マイページを表示できない()
    {
        // 未ログイン状態の場合はログインページに遷移する
        $response = $this->get(route('mypage:posts'));
        $response->assertRedirect(route('login'));
    }
}