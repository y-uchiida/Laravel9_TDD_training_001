<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignupControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザー登録画面にアクセスできる()
    {
        $response = $this->get('/signup');

        $response->assertStatus(200);
    }

    /** @test */
    public function Post送信した内容でユーザーが登録される()
    {
        $name = 'newUser';
        $email = 'newUser@example.com';
        $password = 'newUser@example.com';

        $newUser = compact('name', 'email', 'password');

        $response = $this->post('/signup', $newUser);
        $response->assertOk();

        $this->assertDatabaseHas(User::class, compact(
            'name',
            'email'
        ));
    }
}