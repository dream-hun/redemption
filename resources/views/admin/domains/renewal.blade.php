@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('domain.register') }}" method="POST" id="domainRegistrationForm" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="domain_name" value="{{ $cartItems->first()->name ?? '' }}" required>
            @error('domain_name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div id="domainRegistration">
                <div class="row">
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Domain Contacts</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $contactTypes = [
                                            'registrant' => 'Registrant Contact',
                                            'admin' => 'Administrative Contact',
                                            'tech' => 'Technical Contact',
                                            'billing' => 'Billing Contact'
                                        ];
                                    @endphp

                                    @foreach($contactTypes as $type => $label)
                                    <div class="col-md-3">
                                        <div class="form-group mb-3" x-data="{ contact: { id: '', details: null } }">
                                            <label class="form-label font-weight-bold">{{ $label }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select 
                                                    name="{{ $type }}_contact_id" 
                                                    class="form-control @error($type.'_contact_id') is-invalid @enderror" 
                                                    x-model="contact.id" 
                                                    @change="fetchContactDetails($el.value).then(result => contact.details = result)" 
                                                    required
                                                >
                                                    <option value="">Select {{ $label }}</option>
                                                    @foreach($contacts as $contact)
                                                        <option value="{{ $contact->id }}">
                                                            {{ $contact->name }} ({{ $contact->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @error($type.'_contact_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror

                                            <!-- Contact Details Card -->
                                            <template x-if="contact.details">
                                                <div class="contact-details mt-3">
                                                    <div class="card">
                                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0">Contact Details</h6>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="contact.details = null">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <p class="mb-1"><strong>Name:</strong> <span x-text="contact.details.name"></span></p>
                                                                    <p class="mb-1"><strong>Email:</strong> <span x-text="contact.details.email"></span></p>
                                                                    <p class="mb-1"><strong>Phone:</strong> <span x-text="contact.details.voice || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Organization:</strong> <span x-text="contact.details.organization || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Address:</strong> <span x-text="contact.details.street1 || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>City:</strong> <span x-text="contact.details.city || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Province:</strong> <span x-text="contact.details.province || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Country:</strong> <span x-text="contact.details.country_code || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Postal Code:</strong> <span x-text="contact.details.postal_code || 'N/A'"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Name Servers <small class="text-muted">(Minimum 2, Maximum 4)</small></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">
                                                    Name Server {{ $i }}
                                                    @if ($i <= 2)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" 
                                                    name="nameservers[]" 
                                                    class="form-control @error('nameservers.'.$i-1) is-invalid @enderror"
                                                    placeholder="ns{{ $i }}.example.com"
                                                    {{ $i <= 2 ? 'required' : '' }}
                                                >
                                                @error('nameservers.'.$i-1)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body box-profile">
                                <h3 class="profile-username text-center">Cart Summary</h3>
                                <p class="text-muted text-center">Domain Registration</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    @foreach ($cartItems as $item)
                                        <li class="list-group-item">
                                            <b>{{ $item->name }}</b>
                                            <span class="float-right">{{ $item->price_formatted }}</span>
                                        </li>
                                    @endforeach
                                    <li class="list-group-item">
                                        <b>Total</b>
                                        <span class="float-right">{{ number_format($total, 2) }}</span>
                                    </li>
                                </ul>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="bi bi-check-circle"></i> Complete Registration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
