<?php

namespace App\Observers;

use App\Http\Controllers\UserSettingsController;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $user_settings_controller = new UserSettingsController();
        $user_settings_controller->set_init_settings($user->id);
    }

    public function updated(User $user)
    {
        //
    }

    public function deleted(User $user)
    {
        //
    }

    public function restored(User $user)
    {
        //
    }

    public function forceDeleted(User $user)
    {
        //
    }
}
