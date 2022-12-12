<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\User;
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
}