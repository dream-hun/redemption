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

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Step 1: Domain Search -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transfer a Domain</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.domains.transfer.check') }}" method="POST" id="domainCheckForm"
                    class="needs-validation" novalidate>
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Domain Name <span class="text-danger">*</span></label>
                        <input type="text" name="domain_name"
                            class="form-control @error('domain_name') is-invalid @enderror"
                            value="{{ old('domain_name', session('domainCheck.domain', '')) }}" required placeholder="example.rw">
                        @error('domain_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Check Domain</button>
                </form>
            </div>
        </div>

        <!-- Step 2: Auth Code Form (if eligible) -->
        @if (session('domainCheck') && session('domainCheck.status') === 'eligible' && !session('authCodeSubmitted'))
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Enter Auth Code</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.domains.transfer.auth-code') }}" method="POST" id="authCodeForm"
                        class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="domain_name" value="{{ session('domainCheck.domain') }}">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Auth Code <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="auth_info"
                                class="form-control @error('auth_info') is-invalid @enderror"
                                value="{{ old('auth_info') }}" required>
                            @error('auth_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Obtain the Auth Code from the current/previous owner's registrar.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Auth Code</button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Step 3: Transfer Form (if auth code submitted) -->
        @if (session('domainCheck') && session('domainCheck.status') === 'eligible' && session('authCodeSubmitted'))
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Complete Domain Transfer</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.domains.transfer.initiate') }}" method="POST" id="transferForm"
                        class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="domain_name" value="{{ session('domainCheck.domain') }}">
                        <input type="hidden" name="auth_info" value="{{ session('domainCheck.authInfo') }}">

                        <!-- Contacts -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Domain Contacts</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $contactTypes = [
                                            'registrant' => 'Registrant Contact',
                                            'admin' => 'Administrative Contact',
                                            'tech' => 'Technical Contact',
                                            'billing' => 'Billing Contact',
                                        ];
                                    @endphp
                                    @foreach ($contactTypes as $type => $label)
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">
                                                    {{ $label }}
                                                    @if ($type === 'registrant')
                                                        <span class="text-danger">*</span>
                                                    @else
                                                        <small>(Optional)</small>
                                                    @endif
                                                </label>
                                                <div class="input-group">
                                                    <select name="{{ $type }}_contact_id"
                                                        class="form-control @error($type . '_contact_id') is-invalid @enderror"
                                                        @if ($type === 'registrant') required @endif>
                                                        <option value="">Select {{ $label }}</option>
                                                        @foreach ($contacts as $contact)
                                                            <option value="{{ $contact->id }}"
                                                                @selected(old($type . '_contact_id') == $contact->id)>
                                                                {{ $contact->name }} ({{ $contact->email }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <a href="{{ route('admin.contacts.create') }}"
                                                            class="btn btn-primary">
                                                            <i class="bi bi-plus-lg"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @error($type . '_contact_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                @if ($type !== 'registrant')
                                                    <small class="form-text text-muted">Defaults to Registrant Contact if not specified.</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Name Servers -->
                        <div class="card mb-4" x-data="{ disableDNS: false }">
                            <div class="card-header">
                                <h4>Name Servers <small class="text-muted">(Minimum 2, Maximum 4)</small></h4>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="disable_dns" name="disable_dns"
                                        x-model="disableDNS">
                                    <label class="form-check-label ms-2" for="disable_dns">
                                        Don't delegate this domain now
                                    </label>
                                </div>
                                <div class="row">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">
                                                    Name Server {{ $i }}
                                                    @if ($i <= 2)
                                                        <span class="text-danger" x-show="!disableDNS">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" name="nameservers[]"
                                                    class="form-control @error('nameservers.' . ($i - 1)) is-invalid @enderror"
                                                    placeholder="ns{{ $i }}.example.com"
                                                    :required="!disableDNS && {{ $i <= 2 ? 'true' : 'false' }}"
                                                    :readonly="disableDNS" :disabled="disableDNS">
                                                @error('nameservers.' . ($i - 1))
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="card">
                            <div class="card-body box-profile">
                                <h3 class="profile-username text-center">Transfer Summary</h3>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Domain</b>
                                        <p class="float-right mb-0">{{ session('domainCheck.domain') }}</p>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Price</b>
                                        <p class="float-right mb-0">0.00 Rwf</p>
                                    </li>
                                </ul>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="bi bi-cart-plus"></i> Initiate Transfer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Domain Check Results -->
        @if (session('domainCheck'))
            <div class="card mt-4">
                <div class="card-body">
                    @if (session('domainCheck.status') === 'in_system')
                        <div class="alert alert-warning">
                            The domain <strong>{{ session('domainCheck.domain') }}</strong> is already registered with us.
                            You can manage it in your <a href="{{ route('admin.domains.index') }}">domains list</a>.
                        </div>
                    @elseif (session('domainCheck.status') === 'not_registered')
                        <div class="alert alert-info">
                            The domain <strong>{{ session('domainCheck.domain') }}</strong> is not registered with any
                            registrar. You can <a href="{{ route('domain.register') }}">register it</a> instead.
                        </div>
                    @elseif (session('domainCheck.status') === 'error')
                        <div class="alert alert-danger">
                            Failed to check the status of <strong>{{ session('domainCheck.domain') }}</strong>. Please try again or contact support.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection