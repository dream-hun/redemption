
    <div class="row g-5 justify-content-center">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    @forelse ($items as $item)
                    <div class="row gy-3 mb-4" data-item-id="{{ $item->uuid }}">
                        <div class="col-lg-5">
                            <div class="me-lg-5">
                                <div class="d-flex">
                                    <div class="">
                                        <span class="nav-link">{{ $item->domain }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 col-6 d-flex flex-row flex-lg-column flex-xl-row text-nowrap">
                            <div class="select-container">
                                <select class="custom-select" style="width: 100px;" wire:change="updatePeriod('{{ $item->uuid }}', $event.target.value)">
                                    @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $item->period == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ Str::plural('Year', $i) }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="">
                                <text class="h6">{{ $item->formatedPrice()->format() }}</text>
                            </div>
                        </div>
                        <div class="col-lg col-sm-6 d-flex justify-content-sm-center justify-content-md-start justify-content-lg-center justify-content-xl-end mb-2">
                            <div class="float-md-end">
                                <button wire:click="removeItem('{{ $item->uuid }}')" class="btn btn-light border text-danger icon-hover-danger">
                                    <i class="bi bi-trash3-fill"></i>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="text-center py-4">
                            <p>Your cart is empty</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Search Domains</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="mb-2">Subtotal:</p>
                        <p class="mb-2">{{ $this->formattedSubtotal }}</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <p class="mb-2">VAT Tax (18%):</p>
                        <p class="mb-2">{{ $this->formattedTax }}</p>
                    </div>

                    <hr />

                    <div class="d-flex justify-content-between">
                        <p class="mb-2 fw-bold">Total:</p>
                        <p class="mb-2 fw-bold">{{ $this->formattedTotal }}</p>
                    </div>

                    <div class="mt-3">
                        @if($items->isNotEmpty())
                            <a href="{{ route('contacts.create') }}" class="btn btn-success bg-success btn-lg w-100 mb-2">Proceed to Checkout</a>
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-primary bg-primary btn-lg w-100">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
