@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1>Transfer Domain: {{ $domain->name }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Registrant</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.domains.transfer.transfer', $domain->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="new_registrant_id">Select New Registrant</label>
                        <select name="new_registrant_id" id="new_registrant_id" class="form-control" required>
                            <option value="">Choose a contact</option>
                            @foreach ($contacts as $contact)
                                <option value="{{ $contact->contact_id }}">{{ $contact->name }} ({{ $contact->contact_id }}) - {{ $contact->email }}</option>
                            @endforeach
                        </select>
                        @error('new_registrant_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Add auth_code field if required by RICTA -->
                    <div class="form-group">
                        <label for="auth_code">Auth Code (if required)</label>
                        <input type="text" name="auth_code" id="auth_code" class="form-control" value="{{ old('auth_code') }}">
                        @error('auth_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt"></i> Transfer Domain
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Domain Information</h3>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $domain->name }}</p>
                <p><strong>Current Registrant:</strong> {{ $domain->registrant_id ?? 'N/A' }}</p>
                <p><strong>Owner:</strong> {{ $domain->owner->name ?? 'N/A' }}</p>
                <p><strong>Expiry:</strong> {{ $domain->expires_at ?? 'N/A' }}</p>
                <p><strong>EPP Info:</strong> {{ json_encode($eppInfo) }}</p>
            </div>
        </div>
    </div>
@endsection