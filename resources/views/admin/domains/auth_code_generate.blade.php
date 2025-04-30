@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Generate Auth Code for {{ $domain->name }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('domains.auth_code.send', $domain) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="mb-3">
                <label for="recipient_email" class="form-label">New Owner's Email</label>
                <input type="email" name="recipient_email" id="recipient_email" class="form-control @error('recipient_email') is-invalid @enderror" required>
                @error('recipient_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Generate and Send Auth Code</button>
            <a href="{{ route('domains.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        // Bootstrap form validation
        (function () {
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