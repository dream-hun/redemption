@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1>Domain Transfers</h1>

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
                <h3 class="card-title">Start New Transfer or Get Auth Code</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form action="" method="GET">
                            <div class="form-group">
                                <label for="domain_uuid">Select Domain to Transfer</label>
                                <select name="uuid" id="domain_uuid" class="form-control" required onchange="if(this.value) window.location.href='{{ url('admin/domains/transfer') }}/' + this.value">
                                    <option value="">Choose a domain</option>
                                    @foreach ($domains as $domain)
                                        <option value="{{ $domain->uuid }}">{{ $domain->name }} (Owner: {{ $domain->owner->name ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('admin.domains.transfer.get-auth-code') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="auth_domain_uuid">Select Domain for Auth Code</label>
                                <select name="uuid" id="auth_domain_uuid" class="form-control" required>
                                    <option value="">Choose a domain</option>
                                    @foreach ($domains as $domain)
                                        <option value="{{ $domain->uuid }}">{{ $domain->name }} (Owner: {{ $domain->owner->name ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-key"></i> Get Auth Code
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">All Domains</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Registrant</th>
                            <th>Owner</th>
                            <th>Expiry</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($domains as $domain)
                            <tr>
                                <td>{{ $domain->name }}</td>
                                <td>{{ $domain->registrant_id ?? 'N/A' }}</td>
                                <td>{{ $domain->owner->name ?? 'N/A' }}</td>
                                <td>{{ $domain->expires_at ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.domains.transfer.index', $domain->uuid) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-exchange-alt"></i> Transfer
                                    </a>
                                    <form action="{{ route('admin.domains.transfer.get-auth-code') }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="uuid" value="{{ $domain->uuid }}">
                                        <button type="submit" class="btn btn-info btn-sm">
                                            <i class="fas fa-key"></i> Auth Code
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @foreach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Transfer History</h3>
            </div>
            <div class="card-body">
                @if ($transfers->isEmpty())
                    <p>No transfer history available.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Domain</th>
                                <th>New Registrant ID</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->domain->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->new_registrant_id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transfer->status === 'completed' ? 'success' : ($transfer->status === 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($transfer->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $transfer->message ?? 'N/A' }}</td>
                                    <td>{{ $transfer->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="modal fade" id="authCodeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Domain Auth Code</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Auth Code:</strong> <span id="authCodeValue">{{ session('auth_code') }}</span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        @if (session('auth_code'))
            <script>
                $(document).ready(function() {
                    $('#authCodeModal').modal('show');
                });
            </script>
        @endif
    </div>
@endsection