<x-admin-layout>
@section('page-title')
    Domains
@endsection
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index') }}">Domains</a></li>
        <li class="breadcrumb-item active">Manage {{ $domain->name }}</li>
    </ol>
@endsection
@section('content')
    @if (session('message'))
        <div class="alert alert-success" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <x-domain-information-component :domain="$domain" />
                </div>
                <div class="col-md-6">
                    @include('admin.domains.nameserver')
                </div>

            </div>

            <div class="row mt-4">
                <div class="col-md-12">

                    <div class="row">
                        @foreach ($contactsByType as $type => $contact)
                            <div class="col-md-3 mb-4">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">{{ ucfirst($type) }} Contact @if ($type === 'registrant')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($contact)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-2"><strong>Name:</strong>
                                                        {{ $contact->name }}</p>
                                                    <p class="mb-2"><strong>Email:</strong>
                                                        {{ $contact->email }}</p>
                                                    <p class="mb-2"><strong>Phone:</strong>
                                                        {{ $contact->voice ?: 'N/A' }}</p>
                                                    <p class="mb-2"><strong>Organization:</strong>
                                                        {{ $contact->organization ?: 'N/A' }}</p>
                                                    <p class="mb-2"><strong>Street:</strong>
                                                        {{ $contact->street1 ?: 'N/A' }}</p>
                                                    @if ($contact->street2)
                                                        <p class="mb-2"><strong>Street 2:</strong>
                                                            {{ $contact->street2 }}</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-2"><strong>City:</strong>
                                                        {{ $contact->city ?: 'N/A' }}</p>
                                                    <p class="mb-2"><strong>Province:</strong>
                                                        {{ $contact->province ?: 'N/A' }}</p>
                                                    <p class="mb-2"><strong>Postal Code:</strong>
                                                        {{ $contact->postal_code ?: 'N/A' }}</p>
                                                    <p class="mb-2"><strong>Country:</strong>
                                                        {{ $contact->country_code ?: 'N/A' }}</p>

                                                </div>
                                            </div>
                                        @endif
                                        @if (!$contact)
                                            <div class="mt-2">
                                                <a href="{{ route('admin.contacts.create') }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Add New Contact
                                                </a>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="card-footer">
                                        @if ($contact)
                                            <a href="{{ route('admin.contacts.edit', $contact->uuid) }}"
                                                class="btn w-100 btn-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
