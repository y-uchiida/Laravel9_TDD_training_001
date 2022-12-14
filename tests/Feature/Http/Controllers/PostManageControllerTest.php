<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Comment;
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
        $response->assertRedirect(route('mypage:edit', ['post' => $storedPost->id]));
        $response->assertSessionHas(['status' => 'ブログを新規追加しました']);

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
        $response->assertRedirect(route('mypage:edit', ['post' => $storedPost->id]));
        $response->assertSessionHas(['status' => 'ブログを新規追加しました']);

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

    /** @test */
    public function ログイン中のユーザーが所有するPostの編集画面を開くことができる()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);

        $response = $this->get(route('mypage:edit', ['post' => $post->id]));
        $response->assertOK();
    }

    /** @test */
    public function ログイン中のユーザー以外が所有するPostの編集画面は開けない()
    {
        $post = Post::factory()->create();
        $this->actingAs($this->user);

        $response = $this->get(route('mypage:edit', ['post' => $post->id]));
        $response->assertForbidden();
    }

    /** @test */
    public function ログイン中のユーザーが所有するPostを更新できる()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $newContentOfPost = Post::factory()->create([
            'is_published' => Post::OPEN
        ]);
        $editUrl = route('mypage:edit', ['post' => $post->id]);
        $this->actingAs($this->user);

        $this->get($editUrl)
            ->assertOK();

        $response = $this->post(
            route('mypage:update', ['post' => $post->id]),
            $newContentOfPost->getAttributes()
        );
        $response->assertRedirect($editUrl);
        $response->assertSessionHas(['status' => 'ブログを更新しました']);

        // 内容が更新されていることを確認
        $post->refresh();
        $this->assertSame($newContentOfPost->title, $post->title);
        $this->assertSame($newContentOfPost->body, $post->body);
        $this->assertEquals($newContentOfPost->is_published, $post->is_published);
    }

    /** @test */
    public function ログイン中のユーザー以外が所有するPostは更新できない()
    {
        $postContent = [
            'title' => 'origin tilte',
            'body' => 'origin content',
            'is_published' => Post::OPEN,
        ];
        $post = Post::factory()->create($postContent);
        $newContentOfPost = Post::factory()->create([
            'is_published' => Post::OPEN
        ]);
        $this->actingAs($this->user);

        $response = $this->post(
            route('mypage:update', ['post' => $post->id]),
            $newContentOfPost->getAttributes()
        );
        $response->assertForbidden();

        // 内容が更新されていないことを確認
        $post->refresh();
        $this->assertSame($postContent['title'], $post->title);
        $this->assertSame($postContent['body'], $post->body);
        $this->assertEquals($postContent['is_published'], $post->is_published);
    }

    /** @test */
    public function ログイン中のユーザーが所有するPostを削除できる()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $comment = Comment::factory()->create(['post_id' => $post->id]);
        $otherPostComment = Comment::factory()->create();

        $this->actingAs($this->user);
        $response = $this->delete(route('mypage:delete', ['post' => $post->id]));
        $response->assertRedirect(route('mypage:posts'));
        $response->assertSessionHas(['status' => 'ブログを削除しました']);

        // Post と関連するComment が削除されていることを確認
        $this->assertModelMissing($post);
        $this->assertModelMissing($comment);

        // Post と関連していないコメントは削除されていないことを確認
        $this->assertModelExists($otherPostComment);
    }

    /** @test */
    public function ログイン中のユーザー以外が所有するPostは削除できない()
    {
        $post = Post::factory()->create();
        $this->actingAs($this->user);

        $response = $this->delete(route('mypage:delete', ['post' => $post->id]));
        $response->assertForbidden();

        // 内容が削除されていないことを確認
        $this->assertModelExists($post);
    }
}