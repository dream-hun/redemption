<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Feature extends Model
{
    protected $table = 'features';

    protected $fillable = [
        'name',

    ];

    public function hostings(): BelongsToMany
    {
        return $this->belongsToMany(Hosting::class, 'host_features', 'host_id', 'feature_id');
    }
}
