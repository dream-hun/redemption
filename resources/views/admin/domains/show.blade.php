@extends('layouts.admin')

@section('page-title')
    {{ trans('cruds.domain.title_singular') }} {{ trans('global.show') }}
@endsection

@section('content')
    @if(session('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
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

            @if(isset($eppInfo))
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
                                    @if(!empty($eppInfo['status']))
                                        @foreach($eppInfo['status'] as $status)
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
                                    @if(!empty($eppInfo['nameservers']))
                                        <ul class="list-unstyled mb-0">
                                            @foreach($eppInfo['nameservers'] as $ns)
                                                <li>{{ $ns }}</li>
                                            @endforeach
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
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @foreach(['registrant' => $domain->registrantContact, 'admin' => $domain->adminContact, 'tech' => $domain->techContact] as $type => $contact)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $type }}">
                            @if($contact)
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
                                        @if($contact->street2)
                                            {{ $contact->street2 }}<br>
                                        @endif
                                        {{ $contact->city }}, {{ $contact->province }} {{ $contact->postal_code }}<br>
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
                            </table>
                            @else
                            <p>No contact information available.</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a class="btn btn-default" href="{{ route('admin.domains.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>

                <a class="btn btn-info" href="{{ route('admin.domains.edit', $domain->id) }}">
                    {{ trans('global.edit') }}
                </a>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('admin.domains.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="col-md-6 text-right">
                @can('domain_delete')
                    @if($domain->owner_id === auth()->id())
                        <form action="{{ route('admin.domains.destroy', $domain->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this domain? This action cannot be undone and will remove the domain from the registry.');" style="display: inline-block;">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete Domain
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
