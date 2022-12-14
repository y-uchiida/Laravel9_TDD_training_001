<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostManageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function ユーザー認証済みでなければ、マイページを表示できない()
    {
        // 未ログイン状態の場合はログインページに遷移する
        $response = $this->get(route('mypage:posts'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ユーザー認証済みの場合はマイページを表示する()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('mypage:posts'));
        $response->assertOk();
    }
}