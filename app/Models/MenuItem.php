<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'link',
        'icon',
        'parent',
        'uniqueId',
        'status',
        'user',
        'menu',
        'icone',
        'lang'
    ];
}
