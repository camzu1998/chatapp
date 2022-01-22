<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FriendshipTest extends TestCase
{
    /** @test */
    public function check_user_friends_route()
    {
        $response = $this->get('/friendship');

        $response->assertRedirect('/'); 
    }

    /** @test */
    public function check_create_friendship_route()
    {
        $response = $this->post('/friendship', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_update_friendship_status_route()
    {
        $response = $this->put('/friendship/1', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
}
