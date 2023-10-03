<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCat extends Model
{
    use HasFactory;
    protected $table = 'article_cat';
    protected $fillable = ['title', 'slug', 'cid', 'uniqueId', 'lang', 'status', 'uid' ];

}
