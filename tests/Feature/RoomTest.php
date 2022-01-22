<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoomTest extends TestCase
{
    /** @test */
    public function check_user_room_view_route()
    {
        $response = $this->get('/room/1');

        $response->assertRedirect('/'); 
    }

    /** @test */
    public function check_create_room_route()
    {
        $response = $this->post('/room', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_update_room_status_route()
    {
        $response = $this->put('/room/1', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_delete_room_route()
    {
        $response = $this->delete('/room/1');

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_update_room_route()
    {
        $response = $this->put('/room/1/update');

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_invite_friends_route()
    {
        $response = $this->post('/room/1/invite', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
}
