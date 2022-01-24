<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function check_user_register_form()
    {
        $response = $this->post('/register', []);

        $response->assertRedirect('/register');
    }

    /** @test */
    public function check_user_login_form()
    {
        $response = $this->post('/login', []);

        $response->assertRedirect('/');
    }

    /** @test */
    public function check_user_main_access()
    {
        $response = $this->get('/main');

        $response->assertRedirect('/');
    }

    /** @test */
    public function check_user_save_settings_form()
    {
        $response = $this->post('/user/set_settings', []);

        $response->assertStatus(200)
                 ->assertJson([
                    'status' => 1,
                 ]);
    }
}
