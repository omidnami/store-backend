<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Registered extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public function __construct($user)
    {
        //
        $this->user = $user;
    }

    public function build()
    {
        $setting = array_filter(json_decode($this->user->setting->data), function ($res){
            if ($res->type === 'email'){
                return $res;
            }
        });
        $setting = $setting[0];

        //helper translate data
        /*
         * fullName
         * fname
         * lname
         * phone
         * email
         * emailVerifyCode
         * phoneVerifyCode
         * orderId
         * orderPrice
         */
        error_log(json_encode($this->user->setting->lang));
        return $this->view('emails.wellcom')
            ->subject($setting->subject)
            ->with([
                'name' => $this->user->name,
                'data' => $setting,
                'direction' => $this->user->setting->lang === 'FA' OR  $this->user->setting->lang === 'AR' ? 'rtl' : 'ltr'
                // داده‌های دیگری که می‌خواهید در ایمیل استفاده شوند
            ]);
    }
}
