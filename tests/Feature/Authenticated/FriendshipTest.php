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
        foreach ($xusers as $xuser) {
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

    /** @test */
    public function check_auth_user_can_accept_friendship_invite()
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $friendship = Friendship::factory()->create([
            'user_id' => $user->id,
            'user2_id' => $this->user->id,
            'by_who' => $user->id,
            'status' => 0
        ]);
        $this->assertModelExists($friendship);
        $response = $this->put('/friendship/'.$friendship->user_id, [
            'button' => 'acceptInvite'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseHas('friendship', [
            'user_id' => $user->id,
            'user2_id' => $this->user->id,
            'status' => 1
        ]);
    }

    /** @test */
    public function check_auth_user_can_deceline_friendship_invite()
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $friendship = Friendship::factory()->create([
            'user_id' => $user->id,
            'user2_id' => $this->user->id,
            'by_who' => $user->id,
            'status' => 0
        ]);
        $this->assertModelExists($friendship);
        $response = $this->put('/friendship/'.$friendship->user_id, [
            'button' => 'decelineInvite'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseHas('friendship', [
            'user_id' => $user->id,
            'user2_id' => $this->user->id,
            'status' => 2
        ]);
    }

    /** @test */
    public function check_auth_user_can_cancel_friendship_invite()
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'user2_id' => $user->id,
            'by_who' => $this->user->id,
            'status' => 0
        ]);
        $this->assertModelExists($friendship);
        $response = $this->put('/friendship/'.$friendship->user2_id, [
            'button' => 'cancelInvite'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseMissing('friendship', [
            'user_id' => $this->user->id,
            'user2_id' => $user->id
        ]);
    }

    /** @test */
    public function check_auth_user_can_delete_friendship()
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $friendship = Friendship::factory()->create([
            'user_id' => $this->user->id,
            'user2_id' => $user->id,
            'by_who' => $this->user->id,
            'status' => 1
        ]);
        $this->assertModelExists($friendship);
        $response = $this->put('/friendship/'.$friendship->user2_id, [
            'button' => 'deleteFriendship'
        ]);
        $response->assertStatus(200)->assertJson(['status' => 0]);
        $this->assertDatabaseMissing('friendship', [
            'user_id' => $this->user->id,
            'user2_id' => $user->id
        ]);
    }
}
