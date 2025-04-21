<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

final class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'client_code',

    ];

    protected $hidden = [
        'client_code',
        'password',
        'remember_token',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($model): void {
            $model->client_code = self::generateCustomerNumber();
            $model->uuid = (string) Str::uuid();
        });
    }

    /**
     * @throws Exception
     */
    public static function generateCustomerNumber(): string
    {
        $lastUser = self::orderBy('id', 'desc')->first();
        if (! $lastUser) {
            return 'BLUCL-000001';
        }
        preg_match('/\d+/', $lastUser->client_code, $matches);
        if (! isset($matches[0])) {
            throw new Exception('Invalid format for reg_number');
        }

        $number = (int) ($matches[0]) + 1;

        return 'BLCL-'.mb_str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function getGravatarAttribute(): string
    {
        $email = md5(mb_strtolower(mb_trim($this->email)));

        return "https://www.gravatar.com/avatar/$email";
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
