<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchEngin extends Model
{
    use HasFactory;
    protected $fillable = ['meta_key', 'meta_description', 'canonical', 'pid', 'type'];
}
