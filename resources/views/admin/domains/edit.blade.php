@extends('layouts.admin')
@section('page-title')
    Domains
@endsection
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index')}}">Domains</a></li>
        <li class="breadcrumb-item active">Manage {{ $domain->name}}</li>
    </ol>
@endsection
@section('content')
    @if(session('message'))
        <div class="alert alert-success" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <x-domain-information-component :domain="$domain"/>
                </div>
                <div class="col-md-6">
                    @include('admin.domains.nameserver')
                </div>

            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Domain Contacts</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.domains.contacts.update', $domain->uuid) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    @foreach($contactsByType as $type => $contact)
                                        <div class="col-md-3">
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h5 class="card-title mb-0">{{ ucfirst($type) }} Contact</h5>
                                                </div>
                                                <div class="card-body">
                                                    @if($contact)
                                                        <div class="contact-info" id="{{ $type }}-info">
                                                            <p class="mb-1"><strong>{{ $contact->name }}</strong></p>
                                                            <p class="mb-1 text-muted">{{ $contact->organization }}</p>
                                                            <p class="mb-1">{{ $contact->street1 }}</p>
                                                            @if($contact->street2)
                                                                <p class="mb-1">{{ $contact->street2 }}</p>
                                                            @endif
                                                            <p class="mb-1">{{ $contact->city }}, {{ $contact->province }} {{ $contact->postal_code }}</p>
                                                            <p class="mb-1">{{ $contact->country_code }}</p>
                                                            <p class="mb-1">{{ $contact->email }}</p>
                                                            <p class="mb-1">{{ $contact->voice }}</p>

                                                            <input type="hidden" name="{{ $type }}_contact_id" value="{{ (string) $contact->contact_id }}" data-contact-type="{{ $type }}">
                                                            @if($errors->has($type.'_contact_id'))
                                                                <div class="text-danger">
                                                                    {{ $errors->first($type.'_contact_id') }}
                                                                </div>
                                                            @endif

                                                            <div class="mt-3">
                                                                <a href="{{ route('admin.contacts.edit', $contact->uuid) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary change-contact" data-type="{{ $type }}">
                                                                    <i class="fas fa-exchange-alt"></i> Change
                                                                </button>
                                                                @if($type !== 'registrant')
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-contact" data-type="{{ $type }}">
                                                                    <i class="fas fa-trash"></i> Remove
                                                                </button>
                                                                <a href="{{ route('admin.domains.contacts.remove', ['domain' => $domain->uuid, 'contactType' => $type]) }}" 
                                                                   class="btn btn-sm btn-danger" 
                                                                   onclick="return confirm('Are you sure you want to permanently remove this {{ $type }} contact from the domain?')">
                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="contact-selector" style="display: none;" id="{{ $type }}-selector">
                                                            <div class="form-group">
                                                                <label for="{{ $type }}_contact_select">Select {{ ucfirst($type) }} Contact</label>
                                                                <select class="form-control contact-select" name="{{ $type }}_contact_id" id="{{ $type }}_contact_select" data-contact-type="{{ $type }}">
                                                                    <option value="">-- Select Contact --</option>
                                                                    <!-- This would be populated with AJAX -->
                                                                </select>
                                                                @if($errors->has($type.'_contact_id'))
                                                                    <div class="text-danger">
                                                                        {{ $errors->first($type.'_contact_id') }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-secondary cancel-change" data-type="{{ $type }}">
                                                                <i class="fas fa-times"></i> Cancel
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- Hidden remove confirmation -->
                                                        <div class="remove-confirmation" style="display: none;" id="{{ $type }}-remove-confirm">
                                                            <div class="alert alert-warning">
                                                                <p>Are you sure you want to remove this {{ $type }} contact?</p>
                                                                <input type="hidden" name="remove_{{ $type }}" value="0" id="remove_{{ $type }}_input">
                                                                <div class="mt-2">
                                                                    <button type="button" class="btn btn-sm btn-danger confirm-remove" data-type="{{ $type }}">
                                                                        <i class="fas fa-check"></i> Yes, Remove
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-secondary cancel-remove" data-type="{{ $type }}">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-3" id="{{ $type }}-empty">
                                                            <p class="text-muted">No {{ ucfirst($type) }} contact assigned</p>
                                                            <div class="form-group">
                                                                <label for="{{ $type }}_contact_select">Select {{ ucfirst($type) }} Contact</label>
                                                                <select class="form-control contact-select" name="{{ $type }}_contact_id" id="{{ $type }}_contact_select" data-contact-type="{{ $type }}">
                                                                    <option value="">-- Select Contact --</option>
                                                                    <!-- This would be populated with AJAX -->
                                                                </select>
                                                                @if($errors->has($type.'_contact_id'))
                                                                    <div class="text-danger">
                                                                        {{ $errors->first($type.'_contact_id') }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Contacts
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load contacts via AJAX when page loads
        loadUserContacts();

        // Handle change contact buttons
        document.querySelectorAll('.change-contact').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                document.querySelector(`#${type}-selector`).style.display = 'block';
                document.querySelector(`#${type}-info`).style.display = 'none';
                
                // Hide remove confirmation if it's visible
                const removeConfirm = document.querySelector(`#${type}-remove-confirm`);
                if (removeConfirm) {
                    removeConfirm.style.display = 'none';
                }
            });
        });

        // Handle cancel change buttons
        document.querySelectorAll('.cancel-change').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                document.querySelector(`#${type}-selector`).style.display = 'none';
                document.querySelector(`#${type}-info`).style.display = 'block';
            });
        });
        
        // Handle remove contact buttons
        document.querySelectorAll('.remove-contact').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                
                // Don't allow removing registrant contact
                if (type === 'registrant') {
                    return;
                }
                
                // Show confirmation
                document.querySelector(`#${type}-info`).style.display = 'none';
                document.querySelector(`#${type}-remove-confirm`).style.display = 'block';
            });
        });
        
        // Handle confirm remove buttons
        document.querySelectorAll('.confirm-remove').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                
                // Set the hidden input value to 1 to indicate removal
                document.querySelector(`#remove_${type}_input`).value = '1';
                
                // Submit the form
                this.closest('form').submit();
            });
        });
        
        // Handle cancel remove buttons
        document.querySelectorAll('.cancel-remove').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                
                // Hide confirmation and show contact info
                document.querySelector(`#${type}-remove-confirm`).style.display = 'none';
                document.querySelector(`#${type}-info`).style.display = 'block';
                
                // Reset the hidden input value
                document.querySelector(`#remove_${type}_input`).value = '0';
            });
        });
    });

    function loadUserContacts() {
        // AJAX call to load user contacts
        fetch('{{ route("api.user.contacts") }}')
            .then(response => response.json())
            .then(data => {
                if (data.contacts) {
                    populateContactSelects(data.contacts);
                }
            })
            .catch(error => console.error('Error loading contacts:', error));
    }

    function populateContactSelects(contacts) {
        const selects = document.querySelectorAll('select[id$="_contact_select"]');

        selects.forEach(select => {
            // Clear existing options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }

            // Add contact options
            contacts.forEach(contact => {
                const option = document.createElement('option');
                // Ensure contact_id is treated as a string
                option.value = String(contact.contact_id);
                option.textContent = `${contact.name} (${contact.email})`;
                select.appendChild(option);
            });
        });
    }

    // Add form submit handler to ensure all contact IDs are strings
    document.querySelector('form').addEventListener('submit', function(e) {
        // Get all contact ID inputs (both hidden and selects)
        const contactInputs = document.querySelectorAll('input[name$="_contact_id"], select[name$="_contact_id"]');
        
        // Ensure each value is a string
        contactInputs.forEach(input => {
            if (input.value) {
                input.value = String(input.value);
            }
        });
    });
</script>
@endsection
