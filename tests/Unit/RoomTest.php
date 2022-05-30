<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Room;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_room()
    {
        //Need to create room
        $room = Room::factory()->create();
        //Check if room exist
        $this->assertModelExists($room);
    }

    public function test_deleting_room()
    {
        //Need to create room
        $room = Room::factory()->create();
        $room->delete();
        //Check if room has been deleted
        $this->assertModelMissing($room);
    }

    public function test_updating_room()
    {
        //Need to create room
        $room = Room::factory()->create();
        //Change room name
        $room->room_name = 'test';
        $room->save();
        //Check if changes affected
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'room_name' => 'test',
        ]);
    }
}
