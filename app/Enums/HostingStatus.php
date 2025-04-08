<?php

namespace App\Enums;

enum HostingStatus: string
{
    case Active='active';
    case Inactive='inactive';

    public static function label($value): string
    {
        return match ($value) {
            self::Active => __('Active'),
            self::Inactive => __('Inactive'),
        };
    }

    public static function color($value): string
    {
        return match ($value) {
            self::Active => 'bg-success',
            self::Inactive => 'bg-warning',
        };
    }

}
