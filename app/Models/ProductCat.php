<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCat extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'cid', 'uniqueId', 'lang', 'status' ];

}
