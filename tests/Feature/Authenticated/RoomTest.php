<?php

namespace Tests\Feature\Authenticated;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Room;
use App\Models\Friendship;

class RoomTest extends AuthenticatedTestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_auth_user_can_create_room_and_access_for_it()
    {
        $users_ids = [87];

        //Populate DB
        $users = User::factory()->count(2)->create();
        //Make friendship for roommates
        foreach ($users as $user) {
            Friendship::factory()->create([
                'user_id' => $this->user->id,
                'user2_id' => $user->id,
                'by_who' => $this->user->id,
                'status' => 1
            ]);
            $users_ids[] = $user->id;
        }
        //Send create room request
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/room', [
            'add_friend' => $users_ids
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseHas('rooms', [
            'room_name' => $this->user->nick.'_room',
            'admin_id'  => $this->user->id
        ]);
        //Find that room :)
        $room = Room::Admin($this->user->id)->where('room_name', $this->user->nick.'_room')->first();
        $this->assertDatabaseHas('room_members', [
            'room_id' => $room->id,
            'user_id' => $this->user->id
        ]);
        $response = $this->get('/room/'.$room->id);
        $response->assertStatus(200);
    }

    /** @test */
    public function check_auth_user_cant_create_room()
    {
        //Send create room request
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/room', [
            'room_name' => 'test',
            'add_friend' => []
        ]);

        $response->assertStatus(200)->assertJson(['status' => 1]);

        $this->assertDatabaseMissing('rooms', [
            'room_name' => 'test',
            'admin_id'  => $this->user->id
        ]);
    }


    /** @test */
    public function check_auth_user_cant_access_someone_else_room()
    {
        //Create admin room
        $user = User::factory()->create();
        //Create room
        $room = $user->adminRoom()->create([
            'room_name' => 'test_delete_someone_room',
        ]);
        $this->assertModelExists($room);

        //Access room route
        $response = $this->get('/room/'.$room->id);
        $response->assertRedirect('/main');

        $user->friends()->create([
            'user2_id' => $this->user->id,
            'by_who' => $user->id,
            'status' => Friendship::FRIENDSHIP_INVITE_STATUS
        ]);
        $response = $this->get('/room/'.$room->id);
        $response->assertRedirect('/main');
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
        //Delete room route
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete('/room/'.$room->id);
        $response->assertStatus(200)->assertJson(['status' => 0]);
    }

    /** @test */
    public function check_auth_user_cant_delete_someone_else_room()
    {
        $user = User::factory()->create();
        //Create room
        $room = $user->adminRoom()->create([
            'room_name' => 'test_delete_someone_room',
        ]);
        $this->assertModelExists($room);
        //Delete room route
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete('/room/'.$room->id);
        $response->assertStatus(200)->assertJson(['status' => 2]);
    }

    /** @test */
    public function check_auth_user_can_invite_friends_to_own_room()
    {
        $users_ids = [];

        //Populate DB
        $users = User::factory()->count(2)->create();

        //Make friendship for roommates
        foreach ($users as $user) {
            Friendship::factory()->create([
                'user_id' => $this->user->id,
                'user2_id' => $user->id,
                'by_who' => $this->user->id,
                'status' => Friendship::FRIENDSHIP_STATUS
            ]);
            $users_ids[] = $user->id;
        }
        //Create room
        $room = $this->user->adminRoom()->create([
            'room_name' => 'test_invite_friends'
        ]);
        $this->assertModelExists($room);
        $this->assertDatabaseHas('room_members', [
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status' => 1
        ]);
        //Invite friends
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/room/'.$room->id.'/invite', [
            'add_friend' => $users_ids
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseHas('room_members', [
            'room_id' => $room->id,
        ]);
    }

    /** @test */
    public function check_auth_user_cant_invite_friends_to_someone_else_room()
    {
        $users_ids = [];

        //Populate DB
        $admin = User::factory()->create();
        $users = User::factory()->count(2)->create();

        //Make friendship for roommates
        foreach ($users as $user) {
            Friendship::factory()->create([
                'user_id' => $this->user->id,
                'user2_id' => $user->id,
                'status' => Friendship::FRIENDSHIP_STATUS,
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
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/room/'.$room->id.'/invite', [
            'add_friend' => $users_ids
        ]);
        $response->assertStatus(200)->assertJson(['status' => 2]);
        $this->assertDatabaseMissing('room_members', [
            'room_id' => $room->id,
            'user_id' => $users_ids[0]
        ]);
    }

    /** @test */
    public function check_auth_user_can_accept_invite()
    {
        $admin = User::factory()->create();
        Friendship::factory()->create([
            'user_id' => $this->user->id,
            'user2_id' => $admin->id,
            'status' => Friendship::FRIENDSHIP_STATUS,
            'by_who' => $this->user->id
        ]);
        //Create room
        $room = Room::factory()->create([
            'room_name' => 'test_accept_invite',
            'admin_id'  => $admin->id
        ]);
        $this->assertModelExists($room);
        //Invite friends
        $room_member = $this->user->roomMember()->create([
            'room_id' => $room->id,
        ]);
        $this->assertModelExists($room_member);
        //Send invalid btn
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put('/room/'.$room->id, [
            'button' => 'invalidBtn'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 2]);
        //Send valid btn
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put('/room/'.$room->id, [
            'button' => 'acceptInvite'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseHas('room_members', [
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status'  => 1
        ]);
    }

    /** @test */
    public function check_auth_user_cant_accept_invite_without_invite()
    {
        $admin = User::factory()->create();
        Friendship::factory()->create([
            'user_id' => $this->user->id,
            'user2_id' => $admin->id,
            'status' => Friendship::FRIENDSHIP_STATUS,
            'by_who' => $this->user->id
        ]);
        //Create room
        $room = $admin->adminRoom()->create([
            'room_name' => 'test_accept_without_invite'
        ]);
        $this->assertModelExists($room);
        //Invite friends
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put('/room/'.$room->id, [
            'button' => 'acceptInvite'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 2]);
        $this->assertDatabaseMissing('room_members', [
            'room_id' => $room->id,
            'user_id' => $this->user->id,
            'status'  => 1
        ]);
    }
}
