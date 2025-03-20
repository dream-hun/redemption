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
                    <p class="text-muted mb-4">Your domain <strong>{{ $domain }}</strong> has been successfully registered.</p>
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
