@extends('layouts.admin')
@section('page-title')
    Domains
@endsection
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index')}}">Domains</a></li>
        <li class="breadcrumb-item active">Manage {{ $domain->name}}</li>
    </ol>
@endsection
@section('content')
    @if(session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header p-2">
                    NameServers

                </div><!-- /.card-header -->
                <div class="card-body">
                    @include('admin.domains.nameserver')

                </div><!-- /.card-body -->
            </div>

            <div class="row">
                @foreach($contacts as $type => $contact)
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">{{ ucfirst($type) }} Contacts</h3>
                            </div>

                            <div class="card-body">

                                <p class="text-muted">
                                    {{$contact?->name}}
                                </p>
                                <hr>
                                <p class="text-muted">
                                    {{$contact?->street1}},{{$contact?->street2}}
                                </p>
                                <hr>
                                <p class="text-muted">
                                    {{$contact?->city}},{{$contact?->province}}
                                </p>
                                <hr>
                                <p class="text-muted">
                                    {{$contact?->country_code}},{{$contact?->voice}}
                                </p>
                                <hr>
                                <p class="text-muted">
                                    {{$contact?->email}}
                                <hr>

                                @if($contact && $contact->type)
                                <a href="{{ route('admin.domains.contacts.edit', ['domain' => $domain->uuid, 'type' => $contact->type]) }}"
                                   class="btn btn-primary btn-block">
                                    <b>Edit Contact</b>
                                </a>
                                @endif

                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection


