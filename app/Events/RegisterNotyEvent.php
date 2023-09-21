<?php

namespace App\Events;

use App\Models\Notify;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterNotyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $email;
    public $name;
    public $phone;
    public $setting;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $lang)
    {
        //
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->name = $user->fname.' '.$user->lname;
        $this->user = $user;
        $this->setting = Notify::where('type', 'register')->where('lang', $lang)->first();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('mail');
    }
}
