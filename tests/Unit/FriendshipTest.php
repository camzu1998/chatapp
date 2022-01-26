<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Friendship;
use App\Models\User;

class FriendshipTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_check_get_user_friendships()
    {
        //Prepare Friendship
        $friends = User::factory()->count(2)->create();
        $user = User::factory()->create();
        //Make friendship
        foreach($friends as $friend){
            $friendship = Friendship::factory()->create([
                'user_id' => $user->id,
                'user2_id' => $friend->id,
                'by_who' => $user->id
            ]);
            $this->assertModelExists($friendship);
        }
        
        $friendships = Friendship::user($user->id)->get();
        $this->assertEquals(count($friendships), 2);
    }

    public function test_check_if_friendship_exist()
    {
        //Prepare Friendship
        $friend_one = User::factory()->create();
        $friend_two = User::factory()->create();
        //Make friendship
        $friendship = Friendship::factory()->create([
            'user_id' => $friend_one->id,
            'user2_id' => $friend_two->id,
            'by_who' => $friend_one->id
        ]);
        $this->assertModelExists($friendship);
        $friendships = Friendship::check($friend_one->id, $friend_two->id);
        $this->assertEquals($friendship->created_at, $friendships[0]->created_at);
    }

    public function test_set_status_friendships()
    {
        //Prepare Friendship
        $friend_one = User::factory()->create();
        $friend_two = User::factory()->create();
        //Make friendship
        $friendship = Friendship::factory()->create([
            'user_id' => $friend_one->id,
            'user2_id' => $friend_two->id,
            'by_who' => $friend_one->id
        ]);
        $this->assertModelExists($friendship);
        $friendships = Friendship::set_status($friend_one->id, $friend_two->id, 1);
        $this->assertDatabaseHas('friendship', [
            'user_id' => $friend_one->id,
            'user2_id' => $friend_two->id,
            'status' => 1
        ]);
    }

    public function test_delete_friendship()
    {
        //Prepare Friendship
        $friend_one = User::factory()->create();
        $friend_two = User::factory()->create();
        //Make friendship
        $friendship = Friendship::factory()->create([
            'user_id' => $friend_one->id,
            'user2_id' => $friend_two->id,
            'by_who' => $friend_one->id
        ]);
        $this->assertModelExists($friendship);
        Friendship::delete_friendship($friend_one->id, $friend_two->id);
        $this->assertDatabaseMissing('friendship', [
            'user_id' => $friend_one->id,
            'user2_id' => $friend_two->id,
        ]);
    }
}
