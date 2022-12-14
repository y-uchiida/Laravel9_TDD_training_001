<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
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
        // マイページトップ(マイブログ一覧)
        $response = $this->get(route('mypage:posts'));
        $response->assertRedirect(route('login'));

        // 新規投稿画面
        $response = $this->get(route('mypage:create'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ユーザー認証済みの場合はマイページを表示する()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('mypage:posts'));
        $response->assertOk();
    }

    /** @test */
    public function マイページでは自分のPostだけが表示される()
    {
        $myPost = Post::factory()->create(['user_id' => $this->user->id]);
        $anyOnesPost = Post::factory()->create();

        $this->actingAs($this->user);
        $response = $this->get(route('mypage:posts'));
        $response->assertOk();
        $response->assertSee($myPost->title);
        $response->assertDontSee($anyOnesPost->title);
    }

    /** @test */
    public function ブログ新規投稿画面の挙動()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('mypage:create'));
        $response->assertOk();
    }
}