<?php

namespace App\Providers;

use App\Events\RoomMemberProcessed;
use App\Listeners\UpdateRoomMemberNewestMsg;
use App\Models\Room;
use App\Models\User;
use App\Observers\RoomObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RoomMemberProcessed::class => [
            UpdateRoomMemberNewestMsg::class,
        ]
    ];

    protected $observers = [
        User::class => [UserObserver::class],
        Room::class => [RoomObserver::class],
    ];

    public function boot()
    {
        //
    }
}
