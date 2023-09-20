<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepoLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'maxQuty',
        'address',
        'depo',
        'row',
        'Shelf',
        'depoMan',
        'user',
        'did',
    ];
}
