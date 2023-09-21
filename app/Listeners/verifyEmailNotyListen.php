<?php

namespace App\Listeners;

use App\Events\verifyEmailNotyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class verifyEmailNotyListen
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
     * @param  \App\Events\verifyEmailNotyEvent  $event
     * @return void
     */
    public function handle(verifyEmailNotyEvent $event)
    {
        //
    }
}
