<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // バリデーションエラーのメッセージをテスト用に上書き
        app()->setLocale('testing');
    }

    /**
     * ログイン情報の配列を生成する
     */
    private function generateUserInfoArray()
    {
        $user = User::factory()->make();
        return [
            'email' => $user->email,
            'password' => $this->randomStr()
        ];
    }

    /** ランダムな文字列を返す */
    private function randomStr(int $length = 16)
    {
        return substr(\base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
    }

    /** @test */
    public function ログイン画面を表示できる()
    {
        $response = $this->get('/mypage/login');
        $response->assertOk();
    }

    /** @test */
    public function Postされたユーザー情報でログインできる()
    {
        $userInfo = $this->generateUserInfoArray();
        $user = User::factory()->create([
            ...$userInfo,
            'password' => Hash::make($userInfo['password'])
        ]);

        $response = $this->post('/mypage/login', $userInfo);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function ログイン成功後、マイページに遷移する()
    {
        $userInfo = $this->generateUserInfoArray();
        $user = User::factory()->create([
            ...$userInfo,
            'password' => Hash::make($userInfo['password'])
        ]);

        $response = $this->post('/mypage/login', $userInfo);
        $response->assertRedirect(route('mypage:posts'));
    }

    /** @test */
    public function ログイン失敗時、ログイン画面に遷移してエラーメッセージを表示する()
    {
        $userInfo = $this->generateUserInfoArray();
        $user = User::factory()->create([
            ...$userInfo,
            'password' => Hash::make($userInfo['password'])
        ]);

        $response = $this->post('/mypage/login', [...$userInfo, 'password' => 'wrong_password']);
        $response->assertRedirect(route('mypage:login'));

        $response->assertSessionHasErrors(['auth_fail']);
    }

    /** @test */
    public function 不正なPostデータの場合はログインできない_すべて未入力()
    {
        $response = $this->post('/mypage/login', []);
        $response->assertRedirect();
        $response->assertInvalid();
    }

    /** @test */
    public function 不正なPostデータの場合はログインできない_email()
    {
        $password = $this->randomStr();
        User::factory()->create(['password' => $password]);
        // email が空の場合
        $response = $this->post('/mypage/login', [
            'email' => '',
            'password' => $password
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['email' => 'required']);

        // email の形式エラー
        $response = $this->post('/mypage/login', [
            'email' => 'aaa@',
            'password' => $password
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['email' => 'email']);
    }

    /** @test */
    public function 不正なPostデータの場合はログインできない_password()
    {
        // password が空の場合
        $response = $this->post('/mypage/login', [
            ...$this->generateUserInfoArray(),
            'password' => '',
        ]);
        $response->assertRedirect();
        $response->assertInvalid(['password' => 'required']);
    }
}