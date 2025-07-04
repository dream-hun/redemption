<x-admin-layout>
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

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('domain.register') }}" method="POST" id="domainRegistrationForm" class="needs-validation" novalidate>

            @csrf
            <input type="hidden" name="domain_name" value="{{ $cartItems->first()->name ?? '' }}" required>
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
                                    <div class="col-md-3">
                                    <div class="form-group mb-3" x-data="{ contact: { id: '', details: null } }">
                                        <label class="form-label font-weight-bold">Registrant Contact <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="registrant_contact_id" class="form-control @error('registrant_contact_id') is-invalid @enderror" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                @error('registrant_contact_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <option value="">Select Registrant Contact</option>
                                                @foreach($existingContacts['registrant'] ?? [] as $contact)
                                                    <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <a href="{{ route('admin.contacts.create') }}"  class="btn btn-primary add-contact-btn" >
                                                    <i class="bi bi-plus-lg"></i> Add New
                                                </a>
                                            </div>
                                        </div>
                                        <template x-if="contact.details">
                                            <div class="contact-details mt-3">
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Contact Details</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Name:</strong> <span x-text="contact.details.name"></span></p>
                                                                <p class="mb-1"><strong>Email:</strong> <span x-text="contact.details.email"></span></p>
                                                                <p class="mb-1"><strong>Phone:</strong> <span x-text="contact.details.voice || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Organization:</strong> <span x-text="contact.details.organization || 'N/A'"></span></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Address:</strong> <span x-text="contact.details.street1 || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>City:</strong> <span x-text="contact.details.city || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Province:</strong> <span x-text="contact.details.province || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Country:</strong> <span x-text="contact.details.country_code || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Postal Code:</strong> <span x-text="contact.details.postal_code || 'N/A'"></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="invalid-feedback">Please select a registrant contact</div>
                                    </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group mb-3" x-data="{ contact: { id: '', details: null } }">
                                        <label class="form-label font-weight-bold">Admin Contact <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="admin_contact_id" class="form-control @error('admin_contact_id') is-invalid @enderror" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                @error('admin_contact_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <option value="">Select Admin Contact</option>
                                                @foreach($existingContacts['admin'] ?? [] as $contact)
                                                    <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <a href="{{ route('admin.contacts.create') }}"  class="btn btn-primary add-contact-btn" >
                                                    <i class="bi bi-plus-lg"></i> Add New
                                                </a>
                                            </div>
                                        </div>
                                        <template x-if="contact.details">
                                            <div class="contact-details mt-3">
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Contact Details</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Name:</strong> <span x-text="contact.details.name"></span></p>
                                                                <p class="mb-1"><strong>Email:</strong> <span x-text="contact.details.email"></span></p>
                                                                <p class="mb-1"><strong>Phone:</strong> <span x-text="contact.details.voice || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Organization:</strong> <span x-text="contact.details.organization || 'N/A'"></span></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Address:</strong> <span x-text="contact.details.street1 || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>City:</strong> <span x-text="contact.details.city || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Province:</strong> <span x-text="contact.details.province || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Country:</strong> <span x-text="contact.details.country_code || 'N/A'"></span></p>
                                                                <p class="mb-1"><strong>Postal Code:</strong> <span x-text="contact.details.postal_code || 'N/A'"></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="invalid-feedback">Please select an admin contact</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3" x-data="{ contact: { id: '', details: null } }">
                                            <label class="form-label font-weight-bold">Technical Contact <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="tech_contact_id" class="form-control @error('tech_contact_id') is-invalid @enderror" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                @error('tech_contact_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                    <option value="">Select Technical Contact</option>
                                                    @foreach($existingContacts['tech'] ?? [] as $contact)
                                                        <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                <button type="button" class="btn btn-primary add-contact-btn" data-type="tech" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                    <i class="bi bi-plus-lg"></i> Add New
                                                </button>
                                                </div>
                                            </div>
                                            <template x-if="contact.details">
                                                <div class="contact-details mt-3">
                                                    <div class="card">
                                                        <div class="card-header bg-light">
                                                            <h5 class="mb-0">Contact Details</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Name:</strong> <span x-text="contact.details.name"></span></p>
                                                                    <p class="mb-1"><strong>Email:</strong> <span x-text="contact.details.email"></span></p>
                                                                    <p class="mb-1"><strong>Phone:</strong> <span x-text="contact.details.voice || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Organization:</strong> <span x-text="contact.details.organization || 'N/A'"></span></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Address:</strong> <span x-text="contact.details.street1 || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>City:</strong> <span x-text="contact.details.city || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Province:</strong> <span x-text="contact.details.province || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Country:</strong> <span x-text="contact.details.country_code || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Postal Code:</strong> <span x-text="contact.details.postal_code || 'N/A'"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="invalid-feedback">Please select a technical contact</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group mb-3" x-data="{ contact: { id: '', details: null } }">
                                            <label class="form-label font-weight-bold">Billing Contact <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="billing_contact_id" class="form-control @error('billing_contact_id') is-invalid @enderror" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                @error('billing_contact_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                    <option value="">Select Billing Contact</option>
                                                    @foreach($existingContacts['billing'] ?? [] as $contact)
                                                        <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                <button type="button" class="btn btn-primary add-contact-btn" data-type="billing" data-bs-toggle="modal" data-bs-target="#contactModal">
                                                    <i class="bi bi-plus-lg"></i> Add New
                                                </button>
                                                </div>
                                            </div>
                                            <template x-if="contact.details">
                                                <div class="contact-details mt-3">
                                                    <div class="card">
                                                        <div class="card-header bg-light">
                                                            <h5 class="mb-0">Contact Details</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Name:</strong> <span x-text="contact.details.name"></span></p>
                                                                    <p class="mb-1"><strong>Email:</strong> <span x-text="contact.details.email"></span></p>
                                                                    <p class="mb-1"><strong>Phone:</strong> <span x-text="contact.details.voice || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Organization:</strong> <span x-text="contact.details.organization || 'N/A'"></span></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Address:</strong> <span x-text="contact.details.street1 || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>City:</strong> <span x-text="contact.details.city || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Province:</strong> <span x-text="contact.details.province || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Country:</strong> <span x-text="contact.details.country_code || 'N/A'"></span></p>
                                                                    <p class="mb-1"><strong>Postal Code:</strong> <span x-text="contact.details.postal_code || 'N/A'"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="invalid-feedback">Please select a billing contact</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card" x-data="{ disableDNS: false }">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Name Servers <small class="text-muted">(Minimum 2, Maximum 4)</small></h3>
                                </div>
                            </div>
                            @error('nameservers')
                                <div class="alert alert-danger mx-3 mt-3 mb-0">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="card-body">
                                <div class="form-check form-check-inline mb-3">
                                    <input type="checkbox" class="form-check-input" id="disable_dns" name="disable_dns" x-model="disableDNS">
                                    <label class="form-check-label ms-2" for="disable_dns">
                                        Don't delegate this domain now
                                    </label>
                                </div>

                                <div class="row">
                                    @for ($i = 0; $i < 4; $i++)
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">
                                                    Name Server {{ $i + 1 }}
                                                    @if ($i < 2)
                                                        <span class="text-danger" x-show="!disableDNS" x-cloak>*</span>
                                                    @endif
                                                </label>
                                                <input type="text"
                                                    name="nameservers[{{ $i }}]"
                                                    class="form-control @error('nameservers.' . $i) is-invalid @enderror"
                                                    placeholder="ns{{ $i + 1 }}.example.com"
                                                    value="{{ old('nameservers[]')}}"
                                                    :required="!disableDNS && {{ $i < 2 ? 'true' : 'false' }}"
                                                    :readonly="disableDNS"
                                                    :disabled="disableDNS"
                                                >
                                                @error('nameservers.' . $i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endfor

                                    <div class="col-12" x-show="disableDNS" x-cloak>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> The domain will use the registry's default name servers.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body box-profile">
                                <h3 class="profile-username text-center">Cart Summary</h3>

                                <ul class="list-group list-group-unbordered mb-3">
                                    @foreach ($cartItems as $item)
                                        <li class="list-group-item">
                                            {{ $item->name }} <p class="float-right">
                                                {{ Cknow\Money\Money::RWF($item->price * $item->quantity) }} /
                                                {{$item->quantity}} {{ Str::plural('Year', $item->quantity) }}</p>
                                        </li>
                                    @endforeach

                                    <li class="list-group list-group-item"><b>Total</b><b>
                                            <p class="float-right">
                                                {{ Cknow\Money\Money::RWF($cartTotal) }}
                                            </p>
                                        </b>
                                    </li>

                                </ul>

                                <a href="{{ route('cart.index') }}" class="btn btn-secondary btn-block mb-3">
                                    <i class="bi bi-arrow-left"></i> Back to Cart
                                </a>

                                <button type="submit" class="btn btn-primary btn-block" id="registerDomainBtn">
                                    <i class="bi bi-check-circle"></i> Register Domain
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>


</x-admin-layout>
