<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegister
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $email;
    public $name;
    public $phone;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        //
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->name = $user->name;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //dispatch(new \App\Listeners\SendRegisterListener($this->user));
        return new Channel('mail');
    }
}
