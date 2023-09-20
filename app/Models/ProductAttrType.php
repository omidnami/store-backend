<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttrType extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'cid', 'type', 'data','link','uniqueId','lang', 'status', 'gp', 'dataType'];

}
