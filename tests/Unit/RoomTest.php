<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Room;
use App\Models\UserRoom;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_room()
    {
        //Next need to create room
        $room = Room::factory()->create();
        $this->assertModelExists($room);
    }

    public function test_deleting_room()
    {
        //Next need to create room
        $room = Room::factory()->create();
        //Delete data
        $room->delete();
        $this->assertDeleted($room);
    }

    public function test_updating_room()
    {
        //Next need to create room
        $room = Room::factory()->create();
        $room->room_name = 'test';
        $room->save();
        $this->assertDatabaseHas('room', [
            'id' => $room->id,
            'room_name' => 'test',
        ]);
    }
}
