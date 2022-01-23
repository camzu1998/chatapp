<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Room;
use App\Models\UserRoom;

class RoomTest extends TestCase
{
    public function test_creating_room()
    {
        //First need to create user
        $user = User::factory()->create();
        //Next need to create room
        $room = Room::factory()->create([
            'admin_id' => $user->id
        ]);
        $this->assertModelExists($room);
        //At the end need to insert data to UserRoom
        $userRoom = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'status'  => 1
        ]);
        $this->assertModelExists($userRoom);
    }

    public function test_deleting_room()
    {
        //First need to create user
        $user = User::factory()->create();
        //Next need to create room
        $room = Room::factory()->create([
            'admin_id' => $user->id
        ]);
        //At the end need to insert data to UserRoom
        $userRoom = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'status'  => 1
        ]);
        //Delete data
        $room->delete();
        $this->assertDeleted($room);
        $userRoom->delete();
        $this->assertDeleted($userRoom);
    }
}
