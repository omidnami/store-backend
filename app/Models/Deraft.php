<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deraft extends Model
{
    use HasFactory;
    protected $table = 'drafts';
    protected $fillable = [
        'title',
        'link',
        'linkText',
        'target',
        'status',
        'uniqueId',
        'lang',
        'user'
    ];
}
