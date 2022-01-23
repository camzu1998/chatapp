<?php

namespace Tests\Feature\Authenticated;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Room;
use App\Models\Friendship;

class RoomTest extends AuthenticatedTestCase
{
    use RefreshDatabase;
    /** @test */
    public function check_user_room_route()
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
        //Acess room route
        $response = $this->get('/room/'.$room->id);
        $response->assertStatus(200);
        //Update room

        //Delete room

    }
    
    // /** @test */
    // public function check_update_room_status_route()
    // {
    //     $response = $this->put('/room/1', []);

    //     $response->assertStatus(200)->assertJson(['status' => 9]);
    // }
    
    // /** @test */
    // public function check_user_delete_own_room_route()
    // {
    //     $response = $this->delete('/room/1');

    //     $response->assertStatus(200)->assertJson(['status' => 9]);
    // }
    
    // /** @test */
    // public function check_update_room_route()
    // {
    //     $response = $this->put('/room/1/update');

    //     $response->assertStatus(200)->assertJson(['status' => 9]);
    // }
    
    // /** @test */
    // public function check_invite_friends_route()
    // {
    //     $response = $this->post('/room/1/invite', []);

    //     $response->assertStatus(200)->assertJson(['status' => 9]);
    // }
}
