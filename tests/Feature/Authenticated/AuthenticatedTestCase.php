<?php

namespace Tests\Feature\Authenticated;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\UserSettings;


abstract class AuthenticatedTestCase extends TestCase
{
    protected $user;

    public $user_settings = ['sounds', 'notifications', 'press_on_enter'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        foreach($this->user_settings as $setting_name){
            UserSettings::factory()->create([
                'user_id' => $this->user->id,
                'name' => $setting_name
            ]);
        }
        $this->actingAs($this->user);
    }
}
