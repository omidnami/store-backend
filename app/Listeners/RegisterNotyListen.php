<?php

namespace App\Listeners;

use App\Events\RegisterNotyEvent;
use App\Mail\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class RegisterNotyListen
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\RegisterNotyEvent  $event
     * @return void
     */
    public function handle(RegisterNotyEvent $event)
    {
        Mail::to($event)->send(new Registered($event));
    }
}
