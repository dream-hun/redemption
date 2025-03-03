@extends('layouts.admin')
@section('content')

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
</div>
@endsection
