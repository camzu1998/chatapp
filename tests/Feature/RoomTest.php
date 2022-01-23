<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoomTest extends TestCase
{
    /** @test */
    public function check_unauthenticated_user_cant_access_room()
    {
        $response = $this->get('/room/1');

        $response->assertRedirect('/'); 
    }

    /** @test */
    public function check_unauthenticated_user_cant_create_room()
    {
        $response = $this->post('/room', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_unauthenticated_user_cant_update_room_status()
    {
        $response = $this->put('/room/1', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_unauthenticated_user_cant_delete_room()
    {
        $response = $this->delete('/room/1');

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_unauthenticated_user_cant_update_room()
    {
        $response = $this->put('/room/1/update');

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_unauthenticated_user_cant_invite_friends()
    {
        $response = $this->post('/room/1/invite', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
}
