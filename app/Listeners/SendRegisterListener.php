<?php

namespace App\Listeners;

use App\Events\UserRegister;
use App\Mail\Registered;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRegisterListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    private $setting;

    public function __construct(User $user)
    {
        //
        $this->user = $user;
    }

    public function handle(UserRegister $user)
    {
        //
        //error_log($user);
        Mail::to($user)->send(new Registered($this->user));
    }
}
