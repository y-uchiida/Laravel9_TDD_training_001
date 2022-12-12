<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(), // Userを作ってそのIDを設定
            'title' => $this->faker->realText(20),
            'is_published' => Post::OPEN,
            'body' => $this->faker->realText(200),
        ];
    }

    /**
     * 非公開状態のPostを含むデータを生成する
     */
    public function containClosedPosts()
    {
        return $this->state(function (array $attributes) {
            return [
                // 4回に1回の割合でfalseが選択される
                'is_published' => $this->faker->randomElement([true, true, true, false]),
            ];
        });
    }
}