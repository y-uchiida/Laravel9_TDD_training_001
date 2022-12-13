<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SignupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // バリデーションエラーのメッセージをテスト用に上書き
        app()->setLocale('testing');
    }

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

    /** @test */
    public function 不正なPostデータの場合はユーザー登録できない_すべて未入力()
    {
        $response = $this->post('/signup', []);
        $response->assertRedirect();
        $response->assertInvalid();
    }

    /** @test */
    public function 不正なPostデータの場合はユーザー登録できない_name()
    {
        // name が空の場合
        $response = $this->post('/signup', [
            ...$this->generateUserInfoArray(),
            'name' => '',
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['name' => 'required']);

        // name の文字数制限
        $user = $this->generateUserInfoArray();
        $response = $this->post('/signup', [
            ...$user,
            'name' => str_repeat('a', 20),
        ]);
        $response->assertOk();
        $this->assertDatabaseHas(User::class, ['name' => str_repeat('a', 20)]);

        $response = $this->post('/signup', [
            ...$user,
            'name' => str_repeat('a', 21)
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['name' => 'max']);
    }

    /** @test */
    public function 不正なPostデータの場合はユーザー登録できない_email()
    {
        // email が空の場合
        $response = $this->post('/signup', [
            ...$this->generateUserInfoArray(),
            'email' => '',
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['email' => 'required']);

        // email の形式エラー
        $user = $this->generateUserInfoArray();
        $response = $this->post('/signup', [
            ...$user,
            'email' => 'aaa@',
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['email' => 'email']);
    }

    /** @test */
    public function すでにユーザー登録されているメールアドレスは登録できない()
    {
        $user = User::factory()->create();
        $email = $user->email;

        $response = $this->post('/signup', [
            ...$this->generateUserInfoArray(),
            'email' => $email
        ]);

        $response->assertRedirect();
        $response->assertInvalid(['email' => 'unique']);
    }

    /** @test */
    public function 不正なPostデータの場合はユーザー登録できない_password()
    {
        // password が空の場合
        $response = $this->post('/signup', [
            ...$this->generateUserInfoArray(),
            'password' => '',
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['password' => 'required']);

        // password の文字数制限
        $response = $this->post('/signup', [
            ...$this->generateUserInfoArray(),
            'password' => '1234567', // 8文字以上
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['password' => 'min']);
    }

    /** @test */
    public function ユーザー登録後に、登録したユーザーで認証されている()
    {
        $userInfo = $this->generateUserInfoArray();

        $this->post('/signup', $userInfo);
        $user = User::firstWhere(['email' => $userInfo['email']]);
        $this->assertAuthenticatedAs($user);
    }
}