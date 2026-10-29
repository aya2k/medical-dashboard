<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
     use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'is_active',
        'user_name',
        'category'
    ];
}
