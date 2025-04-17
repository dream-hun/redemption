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

        <form action="{{ route('admin.domains.renewal.renew', $domain->uuid) }}" method="POST" id="domainRenewalForm"
            class="needs-validation" novalidate>
            @method('PUT')
            @csrf
            <input type="hidden" name="domain_name" value="{{ $domain->name }}" required>
            {{-- <input type="hidden" name="period" value="1" required> --}}
            @error('domain_name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div id="domainRegistration">
                <div class="row">
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Domain Contacts</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $contactTypes = [
                                            'registrant' => 'Registrant Contact',
                                            'admin' => 'Administrative Contact',
                                            'tech' => 'Technical Contact',
                                            'billing' => 'Billing Contact',
                                        ];
                                    @endphp

                                    @foreach ($contactTypes as $type => $label)
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">{{ $label }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select name="{{ $type }}_contact_id"
                                                        class="form-control @error($type . '_contact_id') is-invalid @enderror"
                                                        required>
                                                        <option value="">Select {{ $label }}</option>

                                                        @foreach ($contacts as $contact)
                                                            @if ($type !== 'registrant' && $eppInfo['contacts'][$type] !== null)
                                                                <option value="{{ $contact->id }}"
                                                                    @if (in_array($contact->contact_id, $eppInfo['contacts'][$type])) selected @endif>
                                                                    {{ $contact->name }} ({{ $contact->email }})
                                                                </option>
                                                            @endif
                                                            @if ($type !== 'registrant' && $eppInfo['contacts'][$type] === null)
                                                                <option value="{{ $contact->id }}">
                                                                    {{ $contact->name }} ({{ $contact->email }})
                                                                </option>
                                                            @endif
                                                            @if ($type === 'registrant')
                                                                <option value="{{ $contact->id }}"
                                                                    @selected($contact->contact_id . '' === $eppInfo['registrant'] . '')>
                                                                    {{ $contact->name }} ({{ $contact->email }})
                                                                </option>
                                                            @endif
                                                        @endforeach

                                                    </select>
                                                    <div class="input-group-append">
                                                        <a href="{{ route('admin.contacts.create') }}"
                                                            class="btn btn-primary">
                                                            <i class="bi bi-plus-lg"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @error($type . '_contact_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror

                                                <!-- Contact Details Card -->
                                                <template x-if="contact.details">
                                                    <div class="contact-details mt-3">
                                                        <div class="card">
                                                            <div
                                                                class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0">Contact Details</h6>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    @click="contact.details = null">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <p class="mb-1"><strong>Name:</strong> <span
                                                                                x-text="contact.details.name"></span></p>
                                                                        <p class="mb-1"><strong>Email:</strong> <span
                                                                                x-text="contact.details.email"></span></p>
                                                                        <p class="mb-1"><strong>Phone:</strong> <span
                                                                                x-text="contact.details.voice || 'N/A'"></span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>Organization:</strong>
                                                                            <span
                                                                                x-text="contact.details.organization || 'N/A'"></span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>Address:</strong> <span
                                                                                x-text="contact.details.street1 || 'N/A'"></span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>City:</strong> <span
                                                                                x-text="contact.details.city || 'N/A'"></span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>Province:</strong> <span
                                                                                x-text="contact.details.province || 'N/A'"></span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>Country:</strong> <span
                                                                                x-text="contact.details.country_code || 'N/A'"></span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>Postal Code:</strong>
                                                                            <span
                                                                                x-text="contact.details.postal_code || 'N/A'"></span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4" x-data="{ disableDNS: false }">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Name Servers <small class="text-muted">(Minimum 2, Maximum
                                            4)</small></h3>

                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="disable_dns" name="disable_dns"
                                        x-model="disableDNS">
                                    <label class="form-check-label ms-2" for="disable_dns">
                                        Don't delegate this domain now
                                    </label>
                                </div>
                                <div class="row">
                                    @foreach ($eppInfo['nameservers'] as $nmserv)
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">
                                                    Name Server {{ $loop->index + 1 }}
                                                    @if ($loop->index <= 2)
                                                        <span class="text-danger" x-show="!disableDNS">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" name="nameservers[]"
                                                    class="form-control @error('nameservers.' . $loop->index - 1) is-invalid @enderror"
                                                    placeholder="{{ $nmserv }}" value="{{ $nmserv }}"
                                                    :required="!disableDNS && {{ $loop->index <= 2 ? 'true' : 'false' }}"
                                                    :readonly="disableDNS" :disabled="disableDNS">
                                                @error('nameservers.' . $loop->index - 1)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach
                                    @if (count($eppInfo['nameservers']) < 4)
                                        @php
                                            $newNameServersInitIndex = count($eppInfo['nameservers']) + 1;
                                        @endphp
                                        @for ($i = $newNameServersInitIndex; $i <= 4; $i++)
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label font-weight-bold">
                                                        Name Server {{ $i }}
                                                        @if ($i <= 2)
                                                            <span class="text-danger" x-show="!disableDNS">*</span>
                                                        @endif
                                                    </label>
                                                    <input type="text" name="nameservers[]"
                                                        class="form-control @error('nameservers.' . $i - 1) is-invalid @enderror"
                                                        placeholder="ns{{ $i }}.example.com"
                                                        :required="!disableDNS && {{ $i <= 2 ? 'true' : 'false' }}"
                                                        :readonly="disableDNS" :disabled="disableDNS">
                                                    @error('nameservers.' . $i - 1)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endfor
                                    @endif
                                    {{-- @for ($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">
                                                    Name Server {{ $i }}
                                                    @if ($i <= 2)
                                                        <span class="text-danger" x-show="!disableDNS">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" name="nameservers[]"
                                                    class="form-control @error('nameservers.' . $i - 1) is-invalid @enderror"
                                                    placeholder="ns{{ $i }}.example.com"
                                                    :required="!disableDNS && {{ $i <= 2 ? 'true' : 'false' }}"
                                                    :readonly="disableDNS" :disabled="disableDNS">
                                                @error('nameservers.' . $i - 1)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endfor --}}
                                    <div class="col-12" x-show="disableDNS" x-cloak>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> The domain will use the registry's default
                                            name servers.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body box-profile">
                                <h3 class="profile-username text-center">Domain Information</h3>
                                <p class="text-muted text-center">{{ $domain->name }}</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Current Expiry</b>
                                        <p class="float-right mb-0">
                                            {{ $domain->expires_at ?? 'N/A' }}
                                        </p>
                                    </li>
                                    {{-- <li class="list-group-item">
                                        <b>Renewal Period</b>
                                        <p class="float-right mb-0">
                                            {{ request('period', 1) }} {{ Str::plural('Year', request('period', 1)) }}
                                        </p>
                                    </li> --}}
                                    <livewire:cart-quantity-input :domain_id="'renew_' . $domain->name" />

                                </ul>



                                <button type="submit" class="btn btn-primary btn-block" id="renewDomainBtn">
                                    <i class="bi bi-check-circle"></i> Complete Renewal
                                </button>

                                <a href="{{ route('cart.index') }}" class="btn btn-secondary btn-block mt-2">
                                    <i class="bi bi-cart"></i> View Cart
                                </a>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
