<?php

namespace App\View\Components;

use App\Models\Domain;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class LatestDomains extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        $user = Auth::user();

        if ($user->whereHas('roles', function ($query) use ($user) {
            $query->where('title', '=', 'Admin');
        })) {

           $domains = Domain::latest('registered_at')
                ->limit(10)
                ->get();
        } else {

            $domains = Domain::where('owner_id', $user->id)
                ->latest('registered_at')
                ->limit(10)
                ->get();
        }
        return view('components.latest-domains',['domains' => $domains]);
    }
}
