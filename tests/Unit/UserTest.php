<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\UserSettings;


class UserTest extends TestCase
{
    // use RefreshDatabase;
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public $user_settings = ['sounds', 'notifications', 'press_on_enter'];
    
    public function test_creating_user()
    {
        //Creeating user
        $user = User::factory()->create();
        //Check if user created
        $this->assertIsObject($user);
        $this->assertModelExists($user);

        foreach($this->user_settings as $setting_name){
            //Creating user settings
            $userSettings = UserSettings::factory()->create([
                'user_id' => $user->id,
                'name' => $setting_name
            ]);
            //Check if user settings exist
            $this->assertIsObject($userSettings);
            $this->assertModelExists($userSettings);
        }
    }

    public function test_deleting_user()
    {
        //Creeating user
        $user = User::factory()->create();
        foreach($this->user_settings as $setting_name){
            //Creating user settings
            $userSettings = UserSettings::factory()->create([
                'user_id' => $user->id,
                'name' => $setting_name
            ]);
        }
        //Delete user settings
        $deleted = UserSettings::where('user_id', $user->id)->delete();
        $this->assertModelMissing($userSettings);
        //Delete user
        $user->delete();
        $this->assertModelMissing($user);
    }
}
