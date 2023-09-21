<?php

namespace App\Listeners;

use App\Events\RequestNotyEvent;
use App\Mail\Registered;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class RequestNotyListen
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\RequestNotyEvent  $event
     * @return void
     */
    public function handle(RequestNotyEvent $event)
    {

        Mail::to($event)->queue(new Registered($event));

    }
}
