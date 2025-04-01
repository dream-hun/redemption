@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Contact Details</h3>
                    <div>
                        <a href="{{ route('admin.contacts.edit', $contact->uuid) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Contact
                        </a>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($has_differences)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            There are differences between local and EPP registry data. Please review and update if necessary.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Local Database Data</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">UUID</dt>
                                        <dd class="col-sm-8">{{ $contact->uuid }}</dd>

                                        <dt class="col-sm-4">Contact ID</dt>
                                        <dd class="col-sm-8">{{ $contact->contact_id }}</dd>

                                        <dt class="col-sm-4">Name</dt>
                                        <dd class="col-sm-8 {{ isset($differences['name']) ? 'text-danger' : '' }}">
                                            {{ $contact->name }}
                                        </dd>

                                        <dt class="col-sm-4">Organization</dt>
                                        <dd class="col-sm-8 {{ isset($differences['organization']) ? 'text-danger' : '' }}">
                                            {{ $contact->organization ?? 'N/A' }}
                                        </dd>

                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8 {{ isset($differences['email']) ? 'text-danger' : '' }}">
                                            {{ $contact->email }}
                                        </dd>

                                        <dt class="col-sm-4">Phone</dt>
                                        <dd class="col-sm-8 {{ isset($differences['voice']) ? 'text-danger' : '' }}">
                                            {{ $contact->voice }}
                                        </dd>

                                        <dt class="col-sm-4">Address</dt>
                                        <dd class="col-sm-8">
                                            <div class="{{ isset($differences['street1']) ? 'text-danger' : '' }}">{{ $contact->street1 }}</div>
                                            @if($contact->street2)
                                                <div class="{{ isset($differences['street2']) ? 'text-danger' : '' }}">{{ $contact->street2 }}</div>
                                            @endif
                                            <div class="{{ isset($differences['city']) ? 'text-danger' : '' }}">{{ $contact->city }}</div>
                                            <div class="{{ isset($differences['province']) ? 'text-danger' : '' }}">{{ $contact->province }}</div>
                                            <div class="{{ isset($differences['postal_code']) ? 'text-danger' : '' }}">{{ $contact->postal_code }}</div>
                                            <div class="{{ isset($differences['country_code']) ? 'text-danger' : '' }}">{{ $contact->country_code }}</div>
                                        </dd>

                                        <dt class="col-sm-4">Status</dt>
                                        <dd class="col-sm-8">{{ $contact->epp_status }}</dd>

                                        <dt class="col-sm-4">Created At</dt>
                                        <dd class="col-sm-8">{{ $contact->created_at->format('Y-m-d H:i:s') }}</dd>

                                        <dt class="col-sm-4">Updated At</dt>
                                        <dd class="col-sm-8">{{ $contact->updated_at->format('Y-m-d H:i:s') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">EPP Registry Data</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Contact ID</dt>
                                        <dd class="col-sm-8">{{ $epp_contact['contact_id'] ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Name</dt>
                                        <dd class="col-sm-8 {{ isset($differences['name']) ? 'text-danger' : '' }}">
                                            {{ $epp_contact['name'] }}
                                        </dd>

                                        <dt class="col-sm-4">Organization</dt>
                                        <dd class="col-sm-8 {{ isset($differences['organization']) ? 'text-danger' : '' }}">
                                            {{ $epp_contact['organization'] ?? 'N/A' }}
                                        </dd>

                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8 {{ isset($differences['email']) ? 'text-danger' : '' }}">
                                            {{ $epp_contact['email'] }}
                                        </dd>

                                        <dt class="col-sm-4">Phone</dt>
                                        <dd class="col-sm-8 {{ isset($differences['voice']) ? 'text-danger' : '' }}">
                                            {{ $epp_contact['voice'] }}
                                        </dd>

                                        <dt class="col-sm-4">Address</dt>
                                        <dd class="col-sm-8">
                                            <div class="{{ isset($differences['street1']) ? 'text-danger' : '' }}">{{ $epp_contact['street1'] }}</div>
                                            @if($epp_contact['street2'])
                                                <div class="{{ isset($differences['street2']) ? 'text-danger' : '' }}">{{ $epp_contact['street2'] }}</div>
                                            @endif
                                            <div class="{{ isset($differences['city']) ? 'text-danger' : '' }}">{{ $epp_contact['city'] }}</div>
                                            <div class="{{ isset($differences['province']) ? 'text-danger' : '' }}">{{ $epp_contact['province'] }}</div>
                                            <div class="{{ isset($differences['postal_code']) ? 'text-danger' : '' }}">{{ $epp_contact['postal_code'] }}</div>
                                            <div class="{{ isset($differences['country_code']) ? 'text-danger' : '' }}">{{ $epp_contact['country_code'] }}</div>
                                        </dd>

                                        <dt class="col-sm-4">Status</dt>
                                        <dd class="col-sm-8">
                                            @foreach($epp_contact['status'] as $status)
                                                <span class="badge bg-info">{{ $status }}</span>
                                            @endforeach
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($has_differences)
                        <div class="mt-4">
                            <h4>Differences Found:</h4>
                            <ul>
                                @foreach($differences as $field => $value)
                                    <li>
                                        <strong>{{ ucfirst($field) }}:</strong>
                                        Local: {{ $contact->$field ?? 'N/A' }} |
                                        EPP: {{ $epp_contact[$field] ?? 'N/A' }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
