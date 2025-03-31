@extends('layouts.admin')

@section('page-title')
    Domain Registration Success
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
    <li class="breadcrumb-item active">Registration Success</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title h3 mb-4">Domain Registration Successful!</h2>
                    <p class="text-muted mb-4">Your domain <strong>{{ $domain->name }}</strong> has been successfully registered.</p>
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Domain Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <p><strong>Registration Date:</strong> {{ $domain->registration_date->format('Y-m-d') }}</p>
                                    <p><strong>Expiration Date:</strong> {{ $domain->expiration_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($domain->status) }}</span></p>
                                    <p><strong>Nameservers:</strong> {{ count(json_decode($domain->nameservers, true)) }} configured</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('epp_response'))
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Registry Response</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <p><strong>Response Code:</strong> <span class="badge bg-success">{{ session('epp_response.code') }}</span></p>
                                    <p><strong>Message:</strong> {{ session('epp_response.message') }}</p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <p><strong>Domain:</strong> {{ session('epp_response.domain') }}</p>
                                    @if(session('epp_response.data'))
                                        <p><strong>Additional Data:</strong></p>
                                        <pre class="small">{{ json_encode(session('epp_response.data'), JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('domains.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Register Another Domain
                        </a>
                        <a href="{{ route('admin.domains.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-2"></i>View My Domains
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
