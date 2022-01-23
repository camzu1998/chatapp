<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\UserSettings;


class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public $user_settings = ['sounds', 'notifications', 'press_on_enter'];
    
    public function test_creating_user()
    {
        $user = User::factory()->create();
        $this->assertIsObject($user);
        $this->assertModelExists($user);

        foreach($this->user_settings as $setting_name){
            $userSettings = UserSettings::factory()->create([
                'user_id' => $user->id,
                'name' => $setting_name
            ]);
            $this->assertIsObject($userSettings);
            $this->assertModelExists($userSettings);
        }
    }

    public function test_deleting_user()
    {
        //Creeating data
        $user = User::factory()->create();
        foreach($this->user_settings as $setting_name){
            $userSettings = UserSettings::factory()->create([
                'user_id' => $user->id,
                'name' => $setting_name
            ]);
        }
        //Deleting data
        $deleted = UserSettings::where('user_id', $user->id)->delete();
        $this->assertDeleted($userSettings);
        $user->delete();
        $this->assertDeleted($user);
    }
}
