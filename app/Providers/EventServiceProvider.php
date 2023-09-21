<?php

namespace App\Providers;

use App\Events\RegisterNotyEvent;
use App\Events\UserRegister;
use App\Listeners\RegisterNotyListen;
use App\Listeners\SendRegisterListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserRegister::class => [
          SendRegisterListener::class,
        ],
        RegisterNotyEvent::class => [
            RegisterNotyListen::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
