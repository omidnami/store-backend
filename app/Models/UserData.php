<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    protected $fillable = [
        'user_id',
        'social',
        'person_data',
        'rang'
    ];
    protected $table = 'user_datas';

    use HasFactory;
}
