<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SignupControllerTest extends TestCase
{
    use RefreshDatabase;

    /** ランダムな文字列を返す */
    private function randomStr(int $length = 16)
    {
        return substr(\base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
    }

    /**
     * ユーザー情報の配列を生成する
     */
    private function generateUserInfoArray()
    {
        $user = User::factory()->make();
        return [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $this->randomStr()
        ];
    }

    /** @test */
    public function ユーザー登録画面にアクセスできる()
    {
        $response = $this->get('/signup');

        $response->assertStatus(200);
    }

    /** @test */
    public function Post送信した内容でユーザーが登録される()
    {
        $newUser = $this->generateUserInfoArray();

        $response = $this->post('/signup', $newUser);
        $response->assertOk();

        $this->assertDatabaseHas(
            User::class,
            [
                'name' => $newUser['name'],
                'email' => $newUser['email']
            ]
        );
    }

    /** @test */
    public function 登録したユーザーのパスワードがハッシュ化して保存されている()
    {
        $newUser = $this->generateUserInfoArray();

        $response = $this->post('/signup', $newUser);
        $response->assertOk();

        $user = User::firstWhere([
            'name' => $newUser['name'],
            'email' => $newUser['email']
        ]);

        $this->assertTrue(
            Hash::check($newUser['password'], $user->password),
            'Post送信したパスワードと、DBに保存されたハッシュ値を照合して正しいことを確認'
        );
    }
}