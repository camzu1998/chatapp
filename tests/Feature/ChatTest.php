<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function check_send_message_route()
    {
        $response = $this->post('/chat/message/1', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }

    /** @test */
    public function check_chat_messages_route()
    {
        $response = $this->get('/get_msg/1');

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_get_last_message_id_route()
    {
        $response = $this->get('/get_newest_id/1');

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
    
    /** @test */
    public function check_upload_file_route()
    {
        $response = $this->post('/chat/file/1', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
}
