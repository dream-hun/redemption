@extends('layouts.admin')

@section('content')
    <div class="container-fluid p-4">
        <h1>Accept Transfer for {{ $invitation->domain->name }}</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (!Auth::check())
            <div class="alert alert-info p-2">
                Please <a href="{{ route('login') }}">log in</a> or <a href="{{ route('register') }}">create an account</a> to
                accept this transfer.
            </div>
        @else
            <form action="{{ route('domains.transfer.process_accept', $invitation->token) }}" method="POST"
                class="needs-validation" novalidate>
                @csrf
                {{-- <div class="mb-3">
                    <label for="registrant_contact_id" class="form-label">Registrant Contact ID</label>
                    <input type="number" name="registrant_contact_id" id="registrant_contact_id"
                        class="form-control @error('registrant_contact_id') is-invalid @enderror" required>
                    @error('registrant_contact_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="admin_contact_id" class="form-label">Admin Contact ID (Optional)</label>
                    <input type="number" name="admin_contact_id" id="admin_contact_id"
                        class="form-control @error('admin_contact_id') is-invalid @enderror">
                    @error('admin_contact_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="tech_contact_id" class="form-label">Tech Contact ID (Optional)</label>
                    <input type="number" name="tech_contact_id" id="tech_contact_id"
                        class="form-control @error('tech_contact_id') is-invalid @enderror">
                    @error('tech_contact_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="billing_contact_id" class="form-label">Billing Contact ID (Optional)</label>
                    <input type="number" name="billing_contact_id" id="billing_contact_id"
                        class="form-control @error('billing_contact_id') is-invalid @enderror">
                    @error('billing_contact_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

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
                                    'billing' => 'Billing Contact',
                                ];
                            @endphp

                            @foreach ($contactTypes as $type => $label)
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">{{ $label }} <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="{{ $type }}_contact_id"
                                                class="form-control @error($type . '_contact_id') is-invalid @enderror"
                                                required>
                                                <option value="">Select {{ $label }}</option>

                                                @foreach ($contacts as $contact)
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
                                        @error($type . '_contact_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror


                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nameservers (Minimun required 2 - Max 4)</label>
                    <div class="row">
                        @for ($i = 0; $i < 4; $i++)
                            <div class="col-md-3">
                                <input type="text" name="nameservers[]"
                                    class="form-control mb-2 @error('nameservers.' . $i) is-invalid @enderror"
                                    placeholder="e.g., ns1.ricta.org.rw" {{ $i < 2 ? 'required' : '' }}>
                                @error('nameservers.' . $i)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endfor
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">Accept Transfer</button>
                <a href="{{ url('/admin/domains') }}" class="btn btn-secondary">Cancel</a>
            </form>
        @endif
    </div>
    <script>
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endsection
