<?php

namespace Tests\Feature\Authenticated;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Friendship;

class FriendshipTest extends AuthenticatedTestCase
{
    /** @test */
    public function check_auth_user_friends_route()
    {
        $response = $this->get('/friendship');
        $response->assertStatus(200)->assertJson(['status' => 1]);
        //Need to make some friends
        $xusers = User::factory()->count(2)->create();
        foreach($xusers as $xuser){
            $this->assertModelExists($xuser);
            $friendship = Friendship::factory()->create([
                'user_id' => $this->user->id,
                'user2_id' => $xuser->id,
                'by_who' => $xuser->id,
                'status' => 1
            ]);
            $this->assertModelExists($friendship);
        }
        //Check it now
        $response = $this->get('/friendship');
        $response->assertStatus(200)->assertJson(['status' => 0]);
    }

    /** @test */
    public function check_auth_user_can_create_friendship()
    {
        $response = $this->post('/friendship', []);
        $response->assertStatus(200)->assertJson(['status' => 1]);

        $user = User::factory()->create();
        $this->assertModelExists($user);
        $response = $this->post('/friendship', [
            'nickname' => $user->nick
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
    }
    
    // /** @test */
    public function check_auth_user_update_friendship_status_route()
    {
        $response = $this->put('/friendship/1', []);

        $response->assertStatus(200)->assertJson(['status' => 9]);
    }
}
