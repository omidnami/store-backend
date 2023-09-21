<?php

namespace App\Listeners;

use App\Events\ContactNotyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ContactNotyListen
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
     * @param  \App\Events\ContactNotyEvent  $event
     * @return void
     */
    public function handle(ContactNotyEvent $event)
    {
        //
    }
}
