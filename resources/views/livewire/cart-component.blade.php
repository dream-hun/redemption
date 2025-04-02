<div>
    <div class="row col-md-12 g-5 justify-content-center">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="col-lg-9 justify-content-center">
            <div class="card border shadow-0">
                <div class="m-4">
                    @forelse ($items as $item)
                        <div class="row gy-3 mb-4 align-items-center" data-item-id="{{ $item->id }}">
                            <div class="col-lg-3">
                                <div class="me-lg-3">
                                    <div class="d-flex align-items-center">
                                        <p class="nav-link h5 mb-0">{{ $item->name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex flex-row align-items-center">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="quantity-controls d-flex align-items-center">
                                        <button type="button" class="btn btn-outline-primary rounded-circle p-2" style="width: 45px; height: 45px; font-size:14px !important;"
                                            wire:click="updateQuantity('{{ $item->id }}', {{ $item->quantity - 1 }})"
                                            wire:loading.attr="disabled"
                                            wire:target="updateQuantity('{{ $item->id }}', {{ $item->quantity - 1 }})"
                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-dash fs-4" wire:loading.remove wire:target="updateQuantity('{{ $item->id }}', {{ $item->quantity - 1 }})"></i>
                                            <i class="bi bi-hourglass-split" wire:loading wire:target="updateQuantity('{{ $item->id }}', {{ $item->quantity - 1 }})"></i>
                                        </button>
                                        <span class="mx-4 fs-5 fw-semibold" style="font-size: 18px !important;">{{ $item->quantity }} {{ Str::plural('Year', $item->quantity) }}</span>
                                        <button type="button" class="btn btn-outline-primary rounded-circle p-2" style="width: 45px; height: 45px;"
                                            wire:click="updateQuantity('{{ $item->id }}', {{ $item->quantity + 1 }})"
                                            wire:loading.attr="disabled"
                                            wire:target="updateQuantity('{{ $item->id }}', {{ $item->quantity + 1 }})"
                                            {{ $item->quantity >= 10 ? 'disabled' : '' }}>
                                            <i class="bi bi-plus fs-4" wire:loading.remove wire:target="updateQuantity('{{ $item->id }}', {{ $item->quantity + 1 }})"></i>
                                            <i class="bi bi-hourglass-split" wire:loading wire:target="updateQuantity('{{ $item->id }}', {{ $item->quantity + 1 }})"></i>
                                        </button>
                                    </div>
                                    <div class="price-display">
                                        <div class="h5 mb-0 d-flex align-items-center gap-2">
                                            <span class="text-primary fw-bold">{{ \Cknow\Money\Money::RWF($item->price * $item->quantity)->format() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 d-flex justify-content-end">
                                <button wire:click="removeItem('{{ $item->id }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="removeItem('{{ $item->id }}')"
                                    class="btn__long btn-outline-danger btn-danger text-white px-4 w-50 d-flex align-items-center gap-2">
                                    <span wire:loading.remove wire:target="removeItem('{{ $item->id }}')">
                                        <i class="bi bi-trash3-fill"></i>
                                        Remove
                                    </span>
                                    <span wire:loading wire:target="removeItem('{{ $item->id }}')">
                                        <i class="bi bi-hourglass-split"></i>
                                        Removing...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p>Your cart is empty</p>
                            <a href="{{ route('domains.index') }}" class="btn btn-primary">Search Domains</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-0 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="mb-2">Subtotal:</p>
                        <p class="mb-2">{{ $this->formattedSubtotal }}</p>
                    </div>

                    <hr />

                    <div class="d-flex justify-content-between">
                        <p class="mb-2 fw-bold">Total:</p>
                        <p class="mb-2 fw-bold">{{ $this->formattedTotal }}</p>
                    </div>

                    <div class="mt-3">
                        @if ($items && $items->isNotEmpty())
                            <a href="{{ route('domain.register')}}"
                                class="btn__long btn btn-success bg-success btn-lg w-100 mb-2">Proceed to Registration</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
