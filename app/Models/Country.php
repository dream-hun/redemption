<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Country extends Model
{
    protected $fillable = ['name', 'code', 'phone_code'];
}
