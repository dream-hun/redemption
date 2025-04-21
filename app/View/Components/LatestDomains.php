<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Domain;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class LatestDomains extends Component
{
    public function render(): View
    {

        $domains = Domain::with(['owner'])
            ->where('owner_id', auth()->id())
            ->latest('registered_at')
            ->limit(10)
            ->get();

        return view('components.latest-domains', ['domains' => $domains]);
    }
}
