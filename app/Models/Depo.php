<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depo extends Model
{
    use HasFactory;
    protected $fillable = [
        'productSk',
        'dynamicId',
        'quty',
        'partNumber',
        'depoLoc',
        'user',
    ];
}
