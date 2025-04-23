<?php

namespace App\Models;

use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
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

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            $model->client_code = self::generateCustomerNumber();
            $model->uuid = (string) Str::uuid();
        });
    }

    /**
     * @throws Exception
     */
    public static function generateCustomerNumber(): string
    {
        $lastUser = User::orderBy('id', 'desc')->first();
        if (! $lastUser) {
            return 'BLUCL-000001';
        }
        preg_match('/\d+/', $lastUser->client_code, $matches);
        if (! isset($matches[0])) {
            throw new Exception('Invalid format for reg_number');
        }

        $number = intval($matches[0]) + 1;

        return 'BLCL-'.str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute(): bool
    {
       // return $this->roles()->where('id', 1)->exists();
       return true;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function getGravatarAttribute(): string
    {
        $email = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/$email";
    }
}
