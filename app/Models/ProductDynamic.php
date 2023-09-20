<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDynamic extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'value',
        'status',
        'depo',
        'price',
        'img',
        'pid',
        'data',
    ];

}
