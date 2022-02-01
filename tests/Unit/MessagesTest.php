<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Messages;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_message()
    {
        //Need to create room
        $msg = Messages::factory()->create();
        //Check if room exist
        $this->assertModelExists($msg);
    }

    public function test_deleting_messages()
    {
        //Need to create room
        // $tmp = Messages::factory()->create([
        $tmp = Messages::factory()->count(5)->create([
            'room_id' => 99,
        ]);
        $msgs = Messages::Room(99)->delete();
        //Check if messages has been deleted
        $this->assertDatabaseMissing('messages', [
            'room_id' => 99,
        ]);
    }
}
