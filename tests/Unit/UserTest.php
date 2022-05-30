<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\UserSettings;


class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_user()
    {
        //Creating user
        $user = User::factory()->create();
        //Check if user created
        $this->assertIsObject($user);
        $this->assertModelExists($user);

        foreach(UserSettings::SETTINGS_TYPES as $setting_name){
            //Creating user settings
            $userSettings = $user->userSettings()->Name($setting_name)->first();
            //Check if user settings exist
            $this->assertIsObject($userSettings);
            $this->assertModelExists($userSettings);
        }

        $this->assertTrue($userSettings->user->id === $user->id);
    }

    public function test_deleting_user()
    {
        //Creating user
        $user = User::factory()->create();
        $user->delete();
        //Check if user is deleted
        $this->assertModelMissing($user);

        //Check if user settings are deleted
        foreach(UserSettings::SETTINGS_TYPES as $setting_name){
            //Creating user settings
            $userSettings = $user->userSettings()->Name($setting_name)->first();
            $this->assertModelMissing($userSettings);
        }
    }
}
