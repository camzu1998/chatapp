<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessagesTest extends TestCase
{    
    public function test_if_unauth_user_can_send_message(){
        $response = $this->post('/chat/message/99', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    public function test_if_unauth_user_can_pload_file(){
        $response = $this->post('/chat/file/99', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    public function test_if_unauth_user_can_get_message(){
        $response = $this->get('/get_msg/99', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    public function test_if_unauth_user_can_get_newest_message_id(){
        $response = $this->get('/get_newest_id/1', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
}