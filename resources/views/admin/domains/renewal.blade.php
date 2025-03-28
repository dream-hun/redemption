@extends('layouts.admin')

@section('content')
    <div class="container col-md-12 py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <h2 class="card-header">Contact Information</h2>
                    <div class="card-body">
                        <form id="contactForm" method="POST" action="{{ route('domains.register') }}">
                            @csrf

                            @if(isset($existingContacts) && count($existingContacts) > 0)
                                <div class="mb-4">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="use_existing_contacts" name="use_existing_contacts" value="1" {{ old('use_existing_contacts') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="use_existing_contacts">
                                            <strong>Use existing contacts</strong> (Skip filling out the form below)
                                        </label>
                                    </div>

                                    <div id="existing_contacts_section" class="{{ old('use_existing_contacts') ? '' : 'd-none' }}">
                                        <div class="card mb-4 border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="mb-0">Select Existing Contacts</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Registrant Contact</label>
                                                        <select name="registrant_contact_id" class="form-select @error('registrant_contact_id') is-invalid @enderror">
                                                            <option value="">Select a contact</option>
                                                            @foreach($existingContacts['registrant'] ?? [] as $contact)
                                                                <option value="{{ $contact->id }}" {{ old('registrant_contact_id', $contact->id) == $contact->id ? 'selected' : '' }}>
                                                                    {{ $contact->name }} ({{ $contact->email }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('registrant_contact_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Admin Contact</label>
                                                        <select name="admin_contact_id" class="form-select @error('admin_contact_id') is-invalid @enderror">
                                                            <option value="">Select a contact</option>
                                                            @foreach($existingContacts['admin'] ?? [] as $contact)
                                                                <option value="{{ $contact->id }}" {{ old('admin_contact_id', $contact->id) == $contact->id ? 'selected' : '' }}>
                                                                    {{ $contact->name }} ({{ $contact->email }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('admin_contact_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Technical Contact</label>
                                                        <select name="tech_contact_id" class="form-select @error('tech_contact_id') is-invalid @enderror">
                                                            <option value="">Select a contact</option>
                                                            @foreach($existingContacts['tech'] ?? [] as $contact)
                                                                <option value="{{ $contact->id }}" {{ old('tech_contact_id', $contact->id) == $contact->id ? 'selected' : '' }}>
                                                                    {{ $contact->name }} ({{ $contact->email }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('tech_contact_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Billing Contact</label>
                                                        <select name="billing_contact_id" class="form-select @error('billing_contact_id') is-invalid @enderror">
                                                            <option value="">Select a contact</option>
                                                            @foreach($existingContacts['billing'] ?? [] as $contact)
                                                                <option value="{{ $contact->id }}" {{ old('billing_contact_id', $contact->id) == $contact->id ? 'selected' : '' }}>
                                                                    {{ $contact->name }} ({{ $contact->email }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('billing_contact_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="new_contact_section" class="{{ old('use_existing_contacts') ? 'd-none' : '' }}">
                                    <div class="card mb-4 border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <h5 class="mb-0">Create New Contact</h5>
                                        </div>
                                        <div class="card-body">
                                            @endif
                                            <div class="row">
                                                <div class="form-group col-md-6">

                                                    <label class="required">Name</label>
                                                    <input type="text" name="name"
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           value="{{ old('name', Auth::user()->name) }}"
                                                        {{ isset($existingContacts) && count($existingContacts) > 0 ? 'data-required="true"' : 'required' }}>
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
                                                           value="{{ old('street1') }}"
                                                        {{ isset($existingContacts) && count($existingContacts) > 0 ? 'data-required="true"' : 'required' }}>
                                                    @error('street1')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror


                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Street Address two(Optional)</label>


                                                    <input type="text" name="street2"
                                                           class="form-control @error('street2') is-invalid @enderror mt-2"
                                                           value="{{ old('street2') }}">
                                                    @error('street1')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror


                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">City</label>
                                                    <input type="text" name="city"
                                                           class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}"
                                                        {{ isset($existingContacts) && count($existingContacts) > 0 ? 'data-required="true"' : 'required' }}>
                                                    @error('city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Province/State</label>
                                                    <input type="text" name="province"
                                                           class="form-control @error('province') is-invalid @enderror"
                                                           value="{{ old('province') }}"
                                                        {{ isset($existingContacts) && count($existingContacts) > 0 ? 'data-required="true"' : 'required' }}>
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
                                                    <select name="country_code"
                                                            class="form-control @error('country_code') is-invalid @enderror"
                                                        {{ isset($existingContacts) && count($existingContacts) > 0 ? 'data-required="true"' : 'required' }}>
                                                        <option value="">Select a country</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->code }}"
                                                                {{ old('country_code') == $country->code ? 'selected' : '' }}>
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
                                                <input type="tel" name="voice"
                                                       class="form-control @error('voice') is-invalid @enderror" value="{{ old('voice') }}"
                                                    {{ isset($existingContacts) && count($existingContacts) > 0 ? 'data-required="true"' : 'required' }}>
                                                <div class="form-text">Include country code (e.g., +1.2025551234)</div>
                                                @error('voice')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       value="{{ old('email', Auth::user()->email) }}"
                                                    {{ isset($existingContacts) && count($existingContacts) > 0 ? 'data-required="true"' : 'required' }}>
                                                @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if(isset($existingContacts) && count($existingContacts) > 0)
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Continue to Domain Registration</button>
                            </div>
                        </form>

                        @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const useExistingCheckbox = document.getElementById('use_existing_contacts');
                                    const existingContactsSection = document.getElementById('existing_contacts_section');
                                    const newContactSection = document.getElementById('new_contact_section');
                                    const formInputs = document.querySelectorAll('[data-required="true"]');
                                    const contactSelects = document.querySelectorAll('select[name$="_contact_id"]');

                                    function toggleRequiredFields(useExisting) {
                                        // Toggle required attribute for new contact form fields
                                        formInputs.forEach(function(input) {
                                            if (useExisting) {
                                                input.removeAttribute('required');
                                            } else {
                                                input.setAttribute('required', 'required');
                                            }
                                        });

                                        // Toggle required attribute for contact selects
                                        contactSelects.forEach(function(select) {
                                            if (useExisting) {
                                                select.setAttribute('required', 'required');
                                                // Ensure the select has a valid value
                                                if (!select.value) {
                                                    // Select the first option with a value if available
                                                    const firstOption = Array.from(select.options).find(option => option.value);
                                                    if (firstOption) {
                                                        select.value = firstOption.value;
                                                    }
                                                }
                                            } else {
                                                select.removeAttribute('required');
                                            }
                                        });
                                    }

                                    function toggleSections(useExisting) {
                                        if (useExisting) {
                                            existingContactsSection.classList.remove('d-none');
                                            newContactSection.classList.add('d-none');
                                            toggleRequiredFields(true);
                                        } else {
                                            existingContactsSection.classList.add('d-none');
                                            newContactSection.classList.remove('d-none');
                                            toggleRequiredFields(false);
                                        }
                                    }

                                    // Initialize on page load
                                    if (useExistingCheckbox) {
                                        // Set initial state based on checkbox
                                        toggleSections(useExistingCheckbox.checked);

                                        // Add event listener for checkbox change
                                        useExistingCheckbox.addEventListener('change', function() {
                                            toggleSections(this.checked);
                                        });

                                        // Add form submit handler to ensure proper validation
                                        document.getElementById('contactForm').addEventListener('submit', function(e) {
                                            const useExisting = useExistingCheckbox.checked;

                                            if (useExisting) {
                                                // Validate all contact selects have values
                                                let valid = true;
                                                contactSelects.forEach(function(select) {
                                                    if (!select.value) {
                                                        valid = false;
                                                        select.classList.add('is-invalid');
                                                    } else {
                                                        select.classList.remove('is-invalid');
                                                    }
                                                });

                                                if (!valid) {
                                                    e.preventDefault();
                                                    alert('Please select all required contacts');
                                                }
                                            }
                                        });
                                    }
                                });
                            </script>
                        @endpush
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
                                    {{ $item->name }} <p class="float-right">
                                        {{ Cknow\Money\Money::RWF($item->price * $item->quantity) }} /
                                        {{$item->quantity}} {{ Str::plural('Year', $item->quantity) }}</p>
                                </li>
                            @endforeach

                            <li class="list-group list-group-item"><b>Total</b><b>
                                    <p class="float-right">
                                        {{ Cknow\Money\Money::RWF($total) }}
                                    </p>
                                </b>
                            </li>

                        </ul>

                        <a href="{{ route('cart.index') }}" class="btn btn-primary btn-block">
                            <i class="bi bi-arrow-left"></i> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
