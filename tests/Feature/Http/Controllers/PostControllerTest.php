<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Middleware\PostShowLimit;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    // テストでデータベースを使う場合、マイグレーション処理などを行うために必要
    use RefreshDatabase;

    /** @test */
    function トップページにアクセスし、Postタイトルが表示されている()
    {
        // 1. 準備
        $post1 = Post::factory()->create(['title' => 'post title1']);
        $post2 = Post::factory()->create(['title' => 'post title2']);

        // 2. テスト対象の処理を実行
        $response = $this->get('/');

        // 3. 検証 (Post のタイトルが表示されているか)
        $response->assertOk(); // ページが取得できてることをまず確認する
        $response->assertSee('post title1');
        $response->assertSee('post title2');
    }

    /** @test */
    function トップページにアクセスし、Postの一覧に作成者名が表示されている()
    {
        // 1. 準備
        $post1 = Post::factory()->create(['title' => 'post title1']);
        $post2 = Post::factory()->create(['title' => 'post title2']);

        // 2. テスト対象の処理を実行
        $response = $this->get('/');

        // 3. 検証 (Post の作成者名が表示されているか)
        $response->assertOk(); // ページが取得できてることをまず確認する
        $response->assertSee($post1->user->name);
        $response->assertSee($post2->user->name);
    }

    /** @test */
    function トップページにアクセスし、Postの一覧にコメントの件数が表示されている()
    {
        // 1. 準備
        $post1 = Post::factory()->hasComments(3)->create();
        $post2 = Post::factory()->hasComments(10)->create();

        // 2. テスト対象の処理を実行
        $response = $this->get('/');

        // 3. 検証 (Post のコメント件数が表示されているか)
        $response->assertOk(); // ページが取得できてることをまず確認する
        $response->assertSee("{$post1->comments->count()} comments");
        $response->assertSee("{$post2->comments->count()} comments");
    }

    /** @test */
    function トップページにアクセスし、コメントの多い順にPostの一覧が表示されている()
    {
        // 1. 準備
        $post1 = Post::factory()->hasComments(3)->create();
        $post2 = Post::factory()->hasComments(10)->create();
        $post3 = Post::factory()->hasComments(5)->create();

        // 2. テスト対象の処理を実行
        $response = $this->get('/');

        // 3. 検証 (Post のコメント件数が表示されているか)
        $response->assertOk(); // ページが取得できてることをまず確認する
        $response->assertSeeInOrder([
            '10 comments',
            '5 comments',
            '3 comments',
        ]);
    }

    /** @test */
    public function トップページのブログ一覧で、非公開の記事は表示されない()
    {
        // 1. 準備
        $post_unpublished = Post::factory()->create([
            'title' => 'closed post',
            'is_published' => Post::CLOSED,
        ]);

        $post_published = Post::factory()->create([
            'title' => 'published post',
            'is_published' => Post::OPEN,
        ]);

        // 2. 実行
        $response = $this->get('/');

        // 3. 検証
        $response->assertDontSee($post_unpublished->title); // 非公開記事は表示されない
        $response->assertSee($post_published->title); // 公開記事は表示される
    }

    /** @test */
    public function ブログの詳細画面が表示できる()
    {
        // IP制限をつけるMiddlewareの動作を外す
        $this->withoutMiddleware(PostShowLimit::class);

        // 1. 準備
        $post = Post::factory()->create();

        // 2. 実行
        $response = $this->get("/post/$post->id");

        // 3. 検証
        $response->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->user->name);
    }

    /** @test */
    public function ブログ詳細画面に、コメントが古い順に表示される()
    {
        // IP制限をつけるMiddlewareの動作を外す
        $this->withoutMiddleware(PostShowLimit::class);

        // 1. 準備
        $post = Post::factory()->create();
        Comment::factory()->create(['post_id' => $post->id, 'created_at' => Carbon::now()->sub('5 days')]);
        Comment::factory()->create(['post_id' => $post->id, 'created_at' => Carbon::now()->sub('3 days')]);
        Comment::factory()->create(['post_id' => $post->id, 'created_at' => Carbon::now()->sub('2 days')]);

        $ordered_comments = $post->comments()->oldest()->select('comments.name')->get()->toArray();
        $names = array_column($ordered_comments, 'name');

        // 2. 実行
        $response = $this->get("/post/$post->id");

        // 3. 検証
        $response->assertOk()
            ->assertSeeInOrder($names);
    }

    /** @test */
    public function 非公開Postの詳細画面は表示できない()
    {
        // IP制限をつけるMiddlewareの動作を外す
        $this->withoutMiddleware(PostShowLimit::class);

        // 1. 準備
        $post = Post::factory()->create(['is_published' => Post::CLOSED]);

        // 2. 実行
        $response = $this->get("post/$post->id");

        // 3. 検証
        $response->assertForbidden();
    }

    /** @test */
    public function クリスマスメッセージの表示()
    {
        // IP制限をつけるMiddlewareの動作を外す
        $this->withoutMiddleware(PostShowLimit::class);

        // 1. 準備
        $post = Post::factory()->create();

        // 2. 実行
        /* Illuminate\Support\Carbon::setTestNow で、テスト処理内での日付状態を指定できる */
        Carbon::setTestNow('12/24');
        $response1224 = $this->get("post/$post->id");

        Carbon::setTestNow('12/25');
        $response1225 = $this->get("post/$post->id");

        Carbon::setTestNow('12/26');
        $response1226 = $this->get("post/$post->id");

        // 3. 検証
        $response1224->assertDontSee('メリークリスマス');
        $response1226->assertDontSee('メリークリスマス');
        $response1225->assertSee('メリークリスマス');
    }
}