<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginControllerTest extends TestCase
{
    /** @test */
    public function ログイン画面を表示できる()
    {
        $response = $this->get('/mypage/login');
        $response->assertOk();
    }
}