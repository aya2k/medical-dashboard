<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
     use HasFactory;

    protected $fillable = [
        'site_name',
        'email',
        'phone',
        'address',
        'facebook',
        'instagram',
        'linkedin',
        'twitter',
        'whatsapp'
    ];
}
