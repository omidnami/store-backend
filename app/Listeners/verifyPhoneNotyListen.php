<?php

namespace App\Listeners;

use App\Events\verifyPhoneNotyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class verifyPhoneNotyListen
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
     * @param  \App\Events\verifyPhoneNotyEvent  $event
     * @return void
     */
    public function handle(verifyPhoneNotyEvent $event)
    {
        //
    }
}
