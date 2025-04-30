@extends('layouts.admin')

@section('page-title')
    {{ trans('cruds.domain.title_singular') }} {{ trans('global.show') }}
@endsection

@section('content')
    @if (session('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.domain.title') }}
        </div>

        <div class="card-body">
            <div class="mb-2">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.domain.fields.id') }}
                            </th>
                            <td>
                                {{ $domain->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.domain.fields.name') }}
                            </th>
                            <td>
                                {{ $domain->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.domain.fields.status') }}
                            </th>
                            <td>
                                {{ App\Models\Domain::STATUS_SELECT[$domain->status] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.domain.fields.registered_at') }}
                            </th>
                            <td>
                                {{ $domain->registered_at }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.domain.fields.expires_at') }}
                            </th>
                            <td>
                                {{ $domain->expires_at }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.domain.fields.auto_renew') }}
                            </th>
                            <td>
                                <input type="checkbox" disabled="disabled" {{ $domain->auto_renew ? 'checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.domain.fields.owner') }}
                            </th>
                            <td>
                                {{ $domain->owner->name ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                @if (isset($eppInfo))
                    <h4 class="mt-4">Registry Information</h4>
                    <div class="card mb-4">
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th>Registry Object ID</th>
                                        <td>{{ $eppInfo['roid'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Domain Status</th>
                                        <td>
                                            @if (!empty($eppInfo['status']))
                                                @foreach ($eppInfo['status'] as $status)
                                                    <span class="badge badge-info">{{ $status }}</span>
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Nameservers</th>
                                        <td>
                                            @if (!empty($eppInfo['nameservers']))
                                                <ul class="list-unstyled mb-0">
                                                    @if (is_array($eppInfo['nameservers']))
                                                        @foreach ($eppInfo['nameservers'] as $ns)
                                                            <li>{{ $ns }}</li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td>{{ $eppInfo['crID'] ?? 'N/A' }} on {{ $eppInfo['crDate'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated By</th>
                                        <td>{{ $eppInfo['upID'] ?? 'N/A' }} on {{ $eppInfo['upDate'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Expiration Date</th>
                                        <td>{{ $eppInfo['exDate'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Transfer Date</th>
                                        <td>{{ $eppInfo['trDate'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sponsoring Registrar</th>
                                        <td>{{ $eppInfo['clID'] ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <h4 class="mt-4">Contact Information</h4>
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#registrant" data-toggle="tab">Registrant Contact</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#admin" data-toggle="tab">Admin Contact</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tech" data-toggle="tab">Technical Contact</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#billing" data-toggle="tab">Billing Contact</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            @foreach (['registrant', 'admin', 'tech', 'billing'] as $type)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="{{ $type }}">
                                    @if (isset($contactsByType[$type]) && $contactsByType[$type])
                                        @php $contact = $contactsByType[$type]; @endphp
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 200px;">Name</th>
                                                <td>{{ $contact->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Organization</th>
                                                <td>{{ $contact->organization }}</td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td>
                                                    {{ $contact->street1 }}<br>
                                                    @if ($contact->street2)
                                                        {{ $contact->street2 }}<br>
                                                    @endif
                                                    {{ $contact->city }}, {{ $contact->province }}
                                                    {{ $contact->postal_code }}<br>
                                                    {{ $contact->country_code }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Phone</th>
                                                <td>{{ $contact->voice }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $contact->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Contact ID</th>
                                                <td>{{ $contact->contact_id }}</td>
                                            </tr>
                                        </table>
                                    @else
                                        <div class="alert alert-info">
                                            No {{ ucfirst($type) }} contact information available.
                                            <div class="mt-3">
                                                <a href="{{ route('admin.domains.edit', $domain->uuid) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-user-plus"></i> Add {{ ucfirst($type) }} Contact
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-default" href="{{ route('admin.domains.index') }}">
                        <i class="fas fa-arrow-left"></i> {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group">
                        <a class="btn btn-info" href="{{ route('admin.domains.edit', $domain->uuid) }}">
                            <i class="fas fa-edit"></i> {{ trans('global.edit') }}
                        </a>

                        <!-- Renew Domain button -->
                        <form action="{{ route('admin.domains.renewal.addToCart', $domain->uuid) }}" method="POST"
                            style="display: inline-block;">
                            @csrf
                            <input type="hidden" name="period" value="1">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sync"></i> Renew Domain
                            </button>
                        </form>

                        <!-- Get Auth Code Form -->
                        <form action="{{ route('admin.domains.transfer.get-auth-code', $domain->uuid) }}" method="POST"
                            style="display: inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-key"></i> Get Auth Code
                            </button>
                        </form>
                        <!-- Transfer Domain button -->
                        <form action="{{ route('admin.domains.transfer.addToCart', $domain->uuid) }}" method="POST"
                            style="display: inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-exchange-alt"></i> Transfer Domain
                            </button>
                        </form>

                        <form action="{{ route('admin.domains.destroy', $domain->uuid) }}" method="POST"
                            style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('{{ trans('global.areYouSure') }}')">
                                <i class="fas fa-trash"></i> {{ trans('global.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Transfer History for {{ $domain->name }}</h3>
        </div>
        <div class="card-body">
            @php
                $transfers = \App\Models\DomainTransfer::where('domain_id', $domain->id)->latest()->get();
            @endphp
            @if ($transfers->isEmpty())
                <p>No transfer history available.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>New Registrant ID</th>
                            <th>Status</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transfers as $transfer)
                            <tr>
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
    
    
@endsection
