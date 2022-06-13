<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_register_form()
    {
        $response = $this->get('/register');
        $response->assertStatus(200)->assertSee('Załóż konto');

        $response = $this->post('/register', []);
        $response->assertSessionHasErrors(['email']);

        $response = $this->post('/register', [
            'nick' => 'test',
            'email' => 'test@test.test',
            'pass' => 'test',
            'pass_2' => 'test',
        ]);
        $response->assertRedirect('/');
    }

    public function test_user_login_form()
    {
        $response = $this->post('/login', []);
        $response->assertRedirect('/');

        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password'
        ]);

        $response->assertRedirect('/main');
    }

    public function test_user_main_access()
    {
        $response = $this->get('/main');
        $response->assertRedirect('/');
    }

    public function test_user_save_settings_form()
    {
        $response = $this->post('/user/set_settings', []);
        $response->assertStatus(200)
                 ->assertJson([
                    'status' => 1,
                 ]);
    }
}
