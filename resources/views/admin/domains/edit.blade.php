@extends('layouts.admin')
@section('page-title')
    Domains
@endsection
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index') }}">Domains</a></li>
        <li class="breadcrumb-item active">Manage {{ $domain->name }}</li>
    </ol>
@endsection
@section('content')
    @if (session('message'))
        <div class="alert alert-success" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <x-domain-information-component :domain="$domain" />
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
                                <input type="hidden" name="action" value="update_contacts">

                                <div class="row">
                                    @foreach ($contactsByType as $type => $contact)
                                        <div class="col-md-3">
                                            <div class="form-group mb-3" x-data="{ contact: { id: '{{ $contact?->id ?? '' }}', details: null } }">
                                                <label class="form-label font-weight-bold">{{ ucfirst($type) }} Contact @if($type === 'registrant')<span class="text-danger">*</span>@endif</label>
                                                <div class="input-group">
                                                    <select name="{{ $type }}_contact_id" class="form-control @error($type . '_contact_id') is-invalid @enderror" 
                                                        x-model="contact.id"
                                                        @change="fetchContactDetails($el.value).then(result => contact.details = result)"
                                                        {{ $type === 'registrant' ? 'required' : '' }}>
                                                        <option value="">Select {{ ucfirst($type) }} Contact</option>
                                                        @foreach($availableContacts as $availableContact)
                                                            <option value="{{ $availableContact->id }}" 
                                                                {{ $contact && $contact->id == $availableContact->id ? 'selected' : '' }}>
                                                                {{ $availableContact->name }} ({{ $availableContact->email }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary add-contact-btn">
                                                            <i class="bi bi-plus-lg"></i> Add New
                                                        </a>
                                                    </div>
                                                </div>
                                                @error($type . '_contact_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <template x-if="contact.details">
                                                    <div class="contact-details mt-3">
                                                        <div class="card">
                                                            <div class="card-header bg-light">
                                                                <h5 class="mb-0">Contact Details</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Name:</strong> <span x-text="contact.details.name"></span></p>
                                                                        <p class="mb-1"><strong>Email:</strong> <span x-text="contact.details.email"></span></p>
                                                                        <p class="mb-1"><strong>Phone:</strong> <span x-text="contact.details.voice || 'N/A'"></span></p>
                                                                        <p class="mb-1"><strong>Organization:</strong> <span x-text="contact.details.organization || 'N/A'"></span></p>
                                                                    </div>
                                                                    <div class="col-md-6">
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
        // Use the global fetchContactDetails function defined in admin layout

        // Add form submit handler
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update domain contacts');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating domain contacts');
            });
        });
    </script>
@endsection
