<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'title',
        'video',
        'link',
        'image',
        'features',
    ];

     protected $casts = [
        'features' => 'array',
    ];
}
