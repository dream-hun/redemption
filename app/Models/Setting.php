<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'email', 'phone', 'addresss', 'twitter', 'instagram', 'youtube', 'linkedin',
    ];
}
