<?php

namespace Tests\Feature\Authenticated;

use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends AuthenticatedTestCase
{
//    use RefreshDatabase;

    /** @test */
    public function check_user_main_access()
    {
        $response = $this->get('/main');

        $this->assertAuthenticated();
        $response->assertSeeText('Wyloguj');
    }

    /** @test */
    public function check_user_login_route()
    {
        $response = $this->get('/');

        $response->assertRedirect('/main');

        $response = $this->post('/login', []);

        $response->assertRedirect('/main');
    }

    /** @test */
    public function check_user_register_route()
    {
        $response = $this->get('/register');

        $response->assertRedirect('/main');

        $response = $this->post('/register', []);

        $response->assertRedirect('/main');
    }

    /** @test */
    public function check_user_save_settings_form()
    {
        $response = $this->post('/user/set_settings', []);

        $response->assertStatus(200)
                 ->assertJson([
                    'status' => 0,
                 ]);
    }
}
