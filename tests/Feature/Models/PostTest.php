<?php

namespace Tests\Feature\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function Userクラスとのリレーションが設定されている()
    {
        // 1. 準備
        $user = User::factory()->create();

        // 2. 実行
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 3. 検証
        $this->assertInstanceOf(User::class, $post->user);
    }

    /** @test */
    function CommentクラスとのhasManyリレーションが設定されている()
    {
        // 1. 準備
        $comment_count = 3;
        $post = Post::factory()->create();

        // 2. 実行
        Comment::factory($comment_count)->create(['post_id' => $post->id]);

        // 3. 検証
        $this->assertInstanceOf(Collection::class, $post->comments);
        $this->assertSame($comment_count, $post->comments->count());
    }

    /** @test */
    function isOpenスコープで、公開Postのみが取得される()
    {
        // 1. 準備
        $post_published = Post::factory()->create(['is_published' => Post::OPEN]);
        $post_unpublished = Post::factory()->create(['is_published' => Post::CLOSED]);

        // 2. 実行
        $postsOnlyOpen = Post::query()->onlyOpen()->get();

        // 3. 検証
        $this->assertTrue(
            $postsOnlyOpen->contains($post_published),
            '公開 のPostが含まれている'
        );
        $this->assertFalse(
            $postsOnlyOpen->contains($post_unpublished),
            '非公開 のPostがonlyOpen スコープの結果に含まれていない'
        );
        $this->assertInstanceOf(Collection::class, $postsOnlyOpen);
        $this->assertSame(1, $postsOnlyOpen->count());
    }
}