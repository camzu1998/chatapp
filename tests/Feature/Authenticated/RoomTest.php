<?php

namespace Tests\Feature\Authenticated;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Room;
use App\Models\UserRoom;
use App\Models\Friendship;

class RoomTest extends AuthenticatedTestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function check_auth_user_can_create_room()
    {
        $users_ids = [];

        //Populate DB
        $users = User::factory()->count(2)->create();

        //Make friendship for roommates
        foreach($users as $user){
            Friendship::factory()->create([
                'user_id' => $this->user->id,
                'user2_id' => $user->id,
                'by_who' => $this->user->id
            ]);
            $users_ids[] = $user->id;
        }
        //Send create room request
        $response = $this->post('/room', [
            'room_name' => 'test',
            'add_friend' => $users_ids
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseHas('room', [
            'room_name' => 'test',
            'admin_id'  => $this->user->id
        ]);
        //Find that room :)
        $room = Room::where('admin_id', $this->user->id)->where('room_name', 'test')->first();
        $this->assertDatabaseHas('user_room', [
            'room_id' => $room->id,
        ]);
    }

    /** @test */
    public function check_auth_user_can_access_room()
    {
        //Create room
        $room = Room::factory()->create([
            'room_name' => 'test_access_route',
            'admin_id'  => $this->user->id
        ]);
        $this->assertModelExists($room);
        $userRoom = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status'  => 1
        ]);
        //Acess room route
        $response = $this->get('/room/'.$room->id);
        $response->assertStatus(200);
    }

    /** @test */
    public function check_auth_user_can_delete_own_room()
    {
        //Create room
        $room = Room::factory()->create([
            'room_name' => 'test_delete_own_room',
            'admin_id'  => $this->user->id
        ]);
        $this->assertModelExists($room);
        $userRoom = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status'  => 1
        ]);
        //Delete room route
        $response = $this->delete('/room/'.$room->id);
        $response->assertStatus(200)->assertJson(['status' => 0]);
    }

    /** @test */
    public function check_auth_user_cant_delete_someone_else_room()
    {
        $user = User::factory()->create();
        //Create room
        $room = Room::factory()->create([
            'room_name' => 'test_delete_someone_room',
            'admin_id'  => $user->id
        ]);
        $this->assertModelExists($room);
        $userRoom = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'status'  => 1
        ]);
        //Delete room route
        $response = $this->delete('/room/'.$room->id);
        $response->assertStatus(200)->assertJson(['status' => 2]);
    }
    
    /** @test */
    public function check_user_can_invite_friends_to_own_room()
    {
        $users_ids = [];

        //Populate DB
        $users = User::factory()->count(2)->create();

        //Make friendship for roommates
        foreach($users as $user){
            Friendship::factory()->create([
                'user_id' => $this->user->id,
                'user2_id' => $user->id,
                'status' => 1,
                'by_who' => $this->user->id
            ]);
            $users_ids[] = $user->id;
        }
        //Create room
        $room = Room::factory()->create([
            'room_name' => 'test_invite_friends',
            'admin_id'  => $this->user->id
        ]);
        $this->assertModelExists($room);
        $userRoom = UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status'  => 1
        ]);
        $this->assertModelExists($userRoom);
        //Invite friends
        $response = $this->post('/room/'.$room->id.'/invite', [
            'add_friend' => $users_ids
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseHas('user_room', [
            'room_id' => $room->id,
        ]);
    }
    
    /** @test */
    public function check_user_cant_invite_friends_to_someone_else_room()
    {
        $users_ids = [];

        //Populate DB
        $admin = User::factory()->create();
        $users = User::factory()->count(2)->create();

        //Make friendship for roommates
        foreach($users as $user){
            Friendship::factory()->create([
                'user_id' => $this->user->id,
                'user2_id' => $user->id,
                'status' => 1,
                'by_who' => $this->user->id
            ]);
            $users_ids[] = $user->id;
        }
        //Create room
        $room = Room::factory()->create([
            'room_name' => 'test_invite_someone_room',
            'admin_id'  => $admin->id
        ]);
        $this->assertModelExists($room);
        //Invite friends
        $response = $this->post('/room/'.$room->id.'/invite', [
            'add_friend' => $users_ids
        ]);
        $response->assertStatus(200)->assertJson(['status' => 2]);
        $this->assertDatabaseMissing('user_room', [
            'room_id' => $room->id,
        ]);
    }
}
