<x-client-layout>
    @section('page-title')
        Edit contact
    @endsection
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Contact</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('account.contacts.update',$contact->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                               value="{{ old('name', $contact->name) }}" required>
                                        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="organization">Organization</label>
                                        <input type="text" name="organization" id="organization" class="form-control"
                                               value="{{ old('organization', $contact->organization) }}">
                                        @error('organization')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="street1">Street Address</label>
                                        <input type="text" name="street1" id="street1" class="form-control"
                                               value="{{ old('street1', $contact->street1) }}" required>
                                        @error('street1')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="street2">Street Address Line 2</label>
                                        <input type="text" name="street2" id="street2" class="form-control"
                                               value="{{ old('street2', $contact->street2) }}">
                                        @error('street2')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" name="city" id="city" class="form-control"
                                               value="{{ old('city', $contact->city) }}" required>
                                        @error('city')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="province">Province/State</label>
                                        <input type="text" name="province" id="province" class="form-control"
                                               value="{{ old('province', $contact->province) }}" required>
                                        @error('province')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" name="postal_code" id="postal_code" class="form-control"
                                               value="{{ old('postal_code', $contact->postal_code) }}" required>
                                        @error('postal_code')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="country_code">Country</label>
                                        <select name="country_code" id="country_code" class="form-control" required>
                                            @foreach($countries as $code => $name)
                                                <option value="{{ $code }}" {{ old('country_code', $contact->country_code) == $code ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_code')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="voice">Phone Number</label>
                                        <input type="tel" name="voice" id="voice" class="form-control"
                                               value="{{ old('voice', $contact->voice) }}" required
                                               placeholder="+xx.xxxxxxxxxx">
                                        @error('voice')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                               value="{{ old('email', $contact->email) }}" required>
                                        @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fax">Fax Number</label>
                                        <input type="tel" name="fax" id="fax" class="form-control"
                                               value="{{ old('fax', $contact->fax_number) }}"
                                               placeholder="+xx.xxxxxxxxxx">
                                        @error('fax')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fax_ext">Fax Extension</label>
                                        <input type="text" name="fax_ext" id="fax_ext" class="form-control"
                                               value="{{ old('fax_ext', $contact->fax_ext) }}">
                                        @error('fax_ext')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="type" value="{{ $contact->contact_type }}">

                            <div class="mt-4 d-flex justify-content-end">
                                <a href="{{ route('account.contacts.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Contact</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>

