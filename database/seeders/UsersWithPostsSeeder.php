<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersWithPostsSeeder extends Seeder
{
    /**
     * Post を所有するUser レコードを作成する
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create()->each(function ($user) {
            // 各ユーザーごとに、1~5件のダミーデータを生成する
            Post::factory(random_int(1, 5))->create(['user_id' => $user->id]);
        });
    }
}