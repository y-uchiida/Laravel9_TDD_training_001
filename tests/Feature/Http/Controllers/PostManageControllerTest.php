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

        // バリデーションエラーのメッセージをテスト用に上書き
        app()->setLocale('testing');
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
        $response = $this->post(route('mypage:create'));
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
    public function ブログ新規投稿画面で、送信した内容通りにPostを作成できる_publishの場合()
    {
        $this->actingAs($this->user);
        $publishedPost = Post::factory()->make([
            'is_published' => Post::OPEN,
            'user_id' => $this->user->id
        ]);

        $this->get(route('mypage:create'))
            ->assertOk();

        $response = $this->post(route('mypage:store'), $publishedPost->getAttributes());
        $storedPost = $this->user->posts()->first();
        $response->assertRedirect(route('mypage:edit', ['id' => $storedPost->id]));

        $this->assertDatabaseHas(Post::class, $publishedPost->getAttributes());
        $this->assertSame($publishedPost->title, $storedPost->title);
        $this->assertSame($publishedPost->body, $storedPost->body);
        $this->assertEquals($publishedPost->is_published, $storedPost->is_published);
    }

    /** @test */
    public function ブログ新規投稿画面で、送信した内容通りにPostを作成できる_unpublishの場合()
    {
        $this->actingAs($this->user);
        $unpublishedPost = Post::factory()->make([
            'is_published' => Post::CLOSED,
            'user_id' => $this->user->id
        ]);

        $this->get(route('mypage:create'))
            ->assertOk();

        $response = $this->post(route('mypage:store'), $unpublishedPost->getAttributes());
        $storedPost = $this->user->posts()->first();
        $response->assertRedirect(route('mypage:edit', ['id' => $storedPost->id]));

        $this->assertDatabaseHas(Post::class, $unpublishedPost->getAttributes());
        $this->assertSame($unpublishedPost->title, $storedPost->title);
        $this->assertSame($unpublishedPost->body, $storedPost->body);
        $this->assertEquals($unpublishedPost->is_published, $storedPost->is_published);
    }

    /** @test */
    public function ブログ新規投稿画面の入力内容チェック_全て空欄の場合()
    {
        $this->actingAs($this->user);
        $response = $this->from(route('mypage:create'))->post(route('mypage:store'), []);
        $response->assertRedirect(route('mypage:create'));
        $response->assertSessionHasErrors([
            'title' => 'required',
            'body' => 'required',
        ]);
    }

    /** @test */
    public function ブログ新規投稿画面の入力内容チェック_title()
    {
        $this->actingAs($this->user);

        // 文字数制限(255文字まで)
        $response = $this->from(route('mypage:create'))->post(
            route('mypage:store'),
            ['title' => str_repeat('x', 255)]
        );
        $response->assertRedirect(route('mypage:create'));
        $response->assertSessionDoesntHaveErrors(['title']);

        $response = $this->from(route('mypage:create'))->post(
            route('mypage:store'),
            ['title' => str_repeat('x', 256)]
        );
        $response->assertRedirect(route('mypage:create'));
        $response->assertSessionHasErrors(['title' => 'max']);
    }
}