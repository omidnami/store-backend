<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    protected $fillable =[
        'lang',
        'title',
        'uniqueId',
        'user',
        'sub_title',
        'link',
        'linkText',
        'target',
        'parent',
        'dynamictext',
        'bg'
    ];
}
