<?php

namespace App\Listeners;

use App\Events\OrderNotyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrtderNotyListen
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
     * @param  \App\Events\OrderNotyEvent  $event
     * @return void
     */
    public function handle(OrderNotyEvent $event)
    {
        //
    }
}
