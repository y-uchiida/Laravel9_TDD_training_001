<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostListControllerTest extends TestCase
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
}