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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Enter Auth Code for Domain Transfer</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.domains.transfer.addToCart') }}" method="POST" id="authCodeForm"
                    class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="domain_name"
                        value="{{ session('domainCheck.domain', old('domain_name')) }}">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Domain Name <span
                                class="text-danger">*</span></label>
                        <input type="text" name="domain_name"
                            class="form-control @error('domain_name') is-invalid @enderror"
                            value="{{ session('domainCheck.domain', old('domain_name')) }}" readonly>
                        @error('domain_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Auth Code <span
                                class="text-danger">*</span></label>
                        <input type="text" name="auth_info"
                            class="form-control @error('auth_info') is-invalid @enderror"
                            value="{{ old('auth_info') }}" required>
                        @error('auth_info')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Proceed to Transfer</button>
                </form>
            </div>
        </div>
    </div>
@endsection