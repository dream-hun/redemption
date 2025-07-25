<?php

declare(strict_types=1);

namespace App\Actions\Contacts;

use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListContactAction
{
    public function handle(int $perPage = 10): LengthAwarePaginator
    {
        return Contact::query()->orderBy('name')->paginate($perPage);
    }
}
