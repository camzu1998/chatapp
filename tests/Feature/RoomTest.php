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
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/room', []);

        $response->assertStatus(401)->assertJson(['status' => 9]);
    }

    /** @test */
    public function check_unauthenticated_user_cant_update_room_status()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put('/room/1', []);

        $response->assertStatus(401)->assertJson(['status' => 9]);
    }

    /** @test */
    public function check_unauthenticated_user_cant_delete_room()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete('/room/1');

        $response->assertStatus(401)->assertJson(['status' => 9]);
    }

    /** @test */
    public function check_unauthenticated_user_cant_update_room()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put('/room/1/update');

        $response->assertStatus(401)->assertJson(['status' => 9]);
    }

    /** @test */
    public function check_unauthenticated_user_cant_invite_friends()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/room/1/invite', []);

        $response->assertStatus(401)->assertJson(['status' => 9]);
    }
}
