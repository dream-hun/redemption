@extends('layouts.admin')

@section('content')
    <div class="container col-md-12 py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <h2 class="card-header">Domain Contact Information</h2>
                    <div class="card-body">

                        <form id="contactForm" method="POST" action="{{ route('domains.register') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="contact_info[name]"
                                        class="form-control @error('contact_info.name') is-invalid @enderror"
                                        value="{{ old('contact_info.name', Auth::user()->name) }}" required>
                                    @error('contact_info.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Organization</label>
                                    <input type="text" name="contact_info[organization]"
                                        class="form-control @error('contact_info.organization') is-invalid @enderror"
                                        value="{{ old('contact_info.organization') }}" required>
                                    @error('contact_info.organization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Street Address</label>
                                <div id="streets-container">
                                    <input type="text" name="contact_info[streets][]"
                                        class="form-control @error('contact_info.streets.0') is-invalid @enderror"
                                        value="{{ old('contact_info.streets.0') }}" required>
                                    @error('contact_info.streets.0')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="button" id="addStreet"
                                    class="btn btn-link btn-sm p-0 mt-2 text-decoration-none">
                                    + Add another street line
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="contact_info[city]"
                                        class="form-control @error('contact_info.city') is-invalid @enderror"
                                        value="{{ old('contact_info.city') }}" required>
                                    @error('contact_info.city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Province/State</label>
                                    <input type="text" name="contact_info[province]"
                                        class="form-control @error('contact_info.province') is-invalid @enderror"
                                        value="{{ old('contact_info.province') }}" required>
                                    @error('contact_info.province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="contact_info[postal_code]"
                                        class="form-control @error('contact_info.postal_code') is-invalid @enderror"
                                        value="{{ old('contact_info.postal_code') }}" required>
                                    @error('contact_info.postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country</label>
                                    <select name="contact_info[country_code]"
                                        class="form-control @error('contact_info.country_code') is-invalid @enderror"
                                        required>
                                        <option value="">Select a country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->code }}"
                                                {{ old('contact_info.country_code') == $country->code ? 'selected' : '' }}>
                                                {{ $country->name }} ({{ $country->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('contact_info.country_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="contact_info[voice]"
                                    class="form-control @error('contact_info.voice') is-invalid @enderror"
                                    value="{{ old('contact_info.voice') }}" required>
                                <div class="form-text">Include country code (e.g., +1.2025551234)</div>
                                @error('contact_info.voice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="contact_info[email]"
                                    class="form-control @error('contact_info.email') is-invalid @enderror"
                                    value="{{ old('contact_info.email', Auth::user()->email) }}" required>
                                @error('contact_info.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Continue to Domain Registration</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body box-profile">
                        <h3 class="profile-username text-center">Cart Summary</h3>

                        <ul class="list-group list-group-unbordered mb-3">
                            @foreach ($cartItems as $item)
                                <li class="list-group-item">
                                    {{ $item->domain }} <p class="float-right">
                                        {{ Cknow\Money\Money::RWF($item->price * $item->period) }} /
                                        {{ $item->period }} year</p>
                                </li>
                            @endforeach
                            <li class="list-group
                                list-group-item"><b>Tax(VAT)</b>
                                    <p class="float-right">
                                        {{ Cknow\Money\Money::RWF($cartItems->sum(function ($item) {return $item->price * $item->period*0.18;})) }}
                                    </p>

                            <li class="list-group
                                list-group-item"><b>Total</b><b>
                                    <p class="float-right">
                                        {{ Cknow\Money\Money::RWF($cartItems->sum(function ($item) {return $item->price * $item->period * 1.18;})) }}
                                    </p>
                                </b>
                            </li>

                        </ul>

                        <a href="{{ route('cart.index') }}" class="btn btn-primary btn-block"><i
                                class="bi bi-arrow-left"></i> Back to
                            Cart</a>
                    </div>
                    <!-- /.card-body -->
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('addStreet').addEventListener('click', function() {
            const container = document.getElementById('streets-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'contact_info[streets][]';
            input.className = 'form-control mt-2';
            input.required = true;
            container.appendChild(input);
        });
    </script>
@endpush
