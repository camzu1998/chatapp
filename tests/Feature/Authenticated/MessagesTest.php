<?php

namespace Tests\Feature\Authenticated;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;

use App\Models\Messages;
use App\Models\Room;
use App\Models\UserRoom;

class MessagesTest extends AuthenticatedTestCase
{
    // use RefreshDatabase;

    public function test_if_auth_user_can_send_message(){
        $room = Room::factory()->create([
            'admin_id' => $this->user->id,
        ]);
        $user_room = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status' => 1
        ]);
        $response = $this->post('/chat/message/'.$room->id, [
            'content' => 'test'
        ]);
        $response->assertStatus(200)->assertJson(['newest_msg' => true]);
    }
    public function test_if_auth_user_can_upload_file(){
        $room = Room::factory()->create([
            'admin_id' => $this->user->id,
        ]);
        $user_room = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status' => 1
        ]);

        Storage::fake('files');
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post('/chat/file/'.$room->id, [
            'file' => $file
        ]);

        $response->assertStatus(200)->assertJson(['newest_msg' => true]);
    }
    public function test_if_auth_user_can_get_messages(){
        $room = Room::factory()->create([
            'admin_id' => $this->user->id,
        ]);
        $user_room = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status' => 1
        ]);
        $msgs = Messages::factory()->count(4)->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->get('/get_msg/'.$room->id);

        $response->assertStatus(200)->assertJson(['newest_msg' => true]);
    }
    public function test_if_auth_user_can_get_newest_message_id(){
        $room = Room::factory()->create([
            'admin_id' => $this->user->id,
        ]);
        UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status' => 1
        ]);
        Messages::factory()->count(4)->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
        ]);
        $last_msg = Messages::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->get('/get_newest_id/'.$room->id);
        $response->assertStatus(200)->assertSee($last_msg->id);
    }
}
