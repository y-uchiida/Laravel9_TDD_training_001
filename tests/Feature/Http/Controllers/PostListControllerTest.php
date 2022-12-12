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
}