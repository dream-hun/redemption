<x-client-layout>
    @section('page-title')
        Create new contact
    @endsection
    <div class="container col-md-12 py-5">
        <div class="card shadow-sm">
            <h2 class="card-header">Contact Information</h2>
            <div class="card-body">
                <form id="contactForm" method="POST" action="{{ route('account.contacts.store') }}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">

                            <label class="required">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="form-group col-md-6">

                            <label class="required">Organization</label>
                            <input type="text" name="organization"
                                   class="form-control @error('organization') is-invalid @enderror"
                                   value="{{ old('organization') }}">
                            @error('organization')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="required">Street Address</label>


                            <input type="text" name="street1"
                                   class="form-control @error('street1') is-invalid @enderror mt-2"
                                   value="{{old('street1')}}" required>
                            @error('street1')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror


                        </div>
                        <div class="form-group col-md-6">
                            <label>Street Address two(Optional)</label>


                            <input type="text" name="street2"
                                   class="form-control @error('street2') is-invalid @enderror mt-2"
                                   value="{{old('street2')}}">
                            @error('street2')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror


                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city') }}" required>
                            @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Province/State</label>
                            <input type="text" name="province"
                                   class="form-control @error('province') is-invalid @enderror"
                                   value="{{ old('province') }}"
                                   required>
                            @error('province')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code"
                                   class="form-control @error('postal_code') is-invalid @enderror"
                                   value="{{ old('postal_code') }}">
                            @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <select name="country_code" class="form-control @error('country_code') is-invalid @enderror"
                                    required>
                                <option value="">Select a country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->code }}" {{ old('country_code') == $country->code ? 'selected' : '' }}>
                                        {{ $country->name }} ({{ $country->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('country_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="voice" class="form-control @error('voice') is-invalid @enderror"
                               value="{{ old('voice') }}" required>
                        <div class="form-text">Include country code (e.g., +1.2025551234)</div>
                        @error('voice')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Continue to Domain Registration</button>
                </form>
            </div>
        </div>
    </div>
</x-client-layout>


