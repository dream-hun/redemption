<?php

declare(strict_types=1);

namespace App\Actions\Domains;

use App\Models\Domain;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListDomainAction
{
    public function handle(int $perPage = 10): LengthAwarePaginator
    {
        return Domain::query()
            ->with(['nameservers', 'owner', 'contacts'])->orderBy('name')->paginate($perPage);

    }
}
