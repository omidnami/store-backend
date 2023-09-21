<?php

namespace App\Listeners;

use App\Events\ChangeOrderNotyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ChangeOrderNotyListen
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
     * @param  \App\Events\ChangeOrderNotyEvent  $event
     * @return void
     */
    public function handle(ChangeOrderNotyEvent $event)
    {
        //
    }
}
