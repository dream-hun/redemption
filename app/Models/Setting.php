<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Setting extends Model
{
    protected $fillable = [
        'email', 'phone', 'addresss', 'twitter', 'instagram', 'youtube', 'linkedin',
    ];
}
