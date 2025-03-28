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
                                                        <div class="contact-info">
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

                                                            <input type="hidden" name="{{ $type }}_contact_id" value="{{ $contact->contact_id }}">

                                                            <div class="mt-3">
                                                                <a href="{{ route('admin.contacts.edit', $contact->uuid) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary change-contact" data-type="{{ $type }}">
                                                                    <i class="fas fa-exchange-alt"></i> Change
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="contact-selector" style="display: none;" id="{{ $type }}-selector">
                                                            <div class="form-group">
                                                                <label for="{{ $type }}_contact_select">Select {{ ucfirst($type) }} Contact</label>
                                                                <select class="form-control" name="{{ $type }}_contact_id" id="{{ $type }}_contact_select">
                                                                    <option value="">-- Select Contact --</option>
                                                                    <!-- This would be populated with AJAX -->
                                                                </select>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-secondary cancel-change" data-type="{{ $type }}">
                                                                <i class="fas fa-times"></i> Cancel
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-3">
                                                            <p class="text-muted">No {{ ucfirst($type) }} contact assigned</p>
                                                            <div class="form-group">
                                                                <label for="{{ $type }}_contact_select">Select {{ ucfirst($type) }} Contact</label>
                                                                <select class="form-control" name="{{ $type }}_contact_id" id="{{ $type }}_contact_select">
                                                                    <option value="">-- Select Contact --</option>
                                                                    <!-- This would be populated with AJAX -->
                                                                </select>
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
                this.closest('.contact-info').style.display = 'none';
            });
        });

        // Handle cancel change buttons
        document.querySelectorAll('.cancel-change').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                document.querySelector(`#${type}-selector`).style.display = 'none';
                document.querySelector(`#${type}-selector`).previousElementSibling.style.display = 'block';
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
                option.value = contact.contact_id;
                option.textContent = `${contact.name} (${contact.email})`;
                select.appendChild(option);
            });
        });
    }
</script>
@endsection
