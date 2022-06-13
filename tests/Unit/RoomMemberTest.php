<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\RoomMember;

class RoomMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_room()
    {
        //Need to create room
        $roomMember = RoomMember::factory()->create();
        //Check if room exist
        $this->assertModelExists($roomMember);
    }

    public function test_deleting_room()
    {
        //Need to create room
        $roomMember = RoomMember::factory()->create();
        $roomMember->delete();
        //Check if room has been deleted
        $this->assertModelMissing($roomMember);
    }

    public function test_updating_room()
    {
        //Need to create room
        $roomMember = RoomMember::factory()->create();
        //Change room name
        $roomMember->nickname = 'test';
        $roomMember->save();
        //Check if changes affected
        $this->assertDatabaseHas('room_members', [
            'id' => $roomMember->id,
            'nickname' => 'test',
        ]);
    }
}
