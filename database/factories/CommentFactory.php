<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'body' => $this->faker->realText(100),
            'post_id' => Post::factory(), // Post データを作ってそのIDを設定
            'created_at' => $this->faker->dateTimeBetween('-30days', '-1days'),
        ];
    }
}