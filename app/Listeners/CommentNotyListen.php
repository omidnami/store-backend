<?php

namespace App\Listeners;

use App\Events\CommentNotyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentNotyListen
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
     * @param  \App\Events\CommentNotyEvent  $event
     * @return void
     */
    public function handle(CommentNotyEvent $event)
    {
        //
    }
}
