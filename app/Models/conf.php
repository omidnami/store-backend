<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class conf extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'domain',
        'logo',
        'icon',
        'lang'
    ];
}
