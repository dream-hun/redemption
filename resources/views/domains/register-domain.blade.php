@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('domain.register') }}" method="POST" id="domainRegistrationForm">

            @csrf
            <input type="hidden" name="domain_name" value="{{ $cartItems->first()->name ?? '' }}">
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
                                            <select name="registrant_contact_id" class="form-control" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                <option value="">Select Registrant Contact</option>
                                                @foreach($existingContacts['registrant'] ?? [] as $contact)
                                                    <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary add-contact-btn" data-type="registrant" data-toggle="modal" data-target="#contactModal">
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
                                        <div class="invalid-feedback">Please select a registrant contact</div>
                                    </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group mb-3" x-data="{ contact: { id: '', details: null } }">
                                        <label class="form-label font-weight-bold">Admin Contact <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="admin_contact_id" class="form-control" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                <option value="">Select Admin Contact</option>
                                                @foreach($existingContacts['admin'] ?? [] as $contact)
                                                    <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                            <button type="button" class="btn btn-primary add-contact-btn" data-type="admin" data-toggle="modal" data-target="#contactModal">
                                                <i class="bi bi-plus-lg"></i> Add new
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
                                    <div class="invalid-feedback">Please select an admin contact</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3" x-data="{ contact: { id: '', details: null } }">
                                            <label class="form-label font-weight-bold">Technical Contact <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="tech_contact_id" class="form-control" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                    <option value="">Select Technical Contact</option>
                                                    @foreach($existingContacts['tech'] ?? [] as $contact)
                                                        <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                <button type="button" class="btn btn-primary add-contact-btn" data-type="tech" data-toggle="modal" data-target="#contactModal">
                                                    <i class="bi bi-plus-lg"></i> Add new
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
                                                <select name="billing_contact_id" class="form-control" x-model="contact.id" @change="fetchContactDetails($el.value).then(result => contact.details = result)" required>
                                                    <option value="">Select Billing Contact</option>
                                                    @foreach($existingContacts['billing'] ?? [] as $contact)
                                                        <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                <button type="button" class="btn btn-primary add-contact-btn" data-type="billing" data-toggle="modal" data-target="#contactModal">
                                                    <i class="bi bi-plus-lg"></i> Add new
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
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Name Servers</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Specify up to 4 name servers for your domain. Leave empty to use our default name servers.</p>

                                <div class="form-group mb-2">
                                    <label class="form-label">Name Server 1</label>
                                    <input type="text" name="nameservers[0]" class="form-control nameserver-input" placeholder="ns1.example.com">
                                </div>

                                <div class="form-group mb-2">
                                    <label class="form-label">Name Server 2</label>
                                    <input type="text" name="nameservers[1]" class="form-control nameserver-input" placeholder="ns2.example.com">
                                </div>

                                <div class="form-group mb-2">
                                    <label class="form-label">Name Server 3</label>
                                    <input type="text" name="nameservers[2]" class="form-control nameserver-input" placeholder="ns3.example.com">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label">Name Server 4</label>
                                    <input type="text" name="nameservers[3]" class="form-control nameserver-input" placeholder="ns4.example.com">
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

    <!-- New Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Create New <span id="contactTypeDisplay">Contact</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('domain.contact.create') }}" method="POST" id="contactForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="contact_type" id="contactTypeInput">
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Organization</label>
                                <input type="text" name="organization" class="form-control">
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" name="voice" class="form-control" required placeholder="+250xxxxxxxxx">
                                @error('voice')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Street Address 1 <span class="text-danger">*</span></label>
                                <input type="text" name="street1" class="form-control" required>
                                @error('street1')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Street Address 2</label>
                                <input type="text" name="street2" class="form-control">
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" required>
                                @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Province/State <span class="text-danger">*</span></label>
                                <input type="text" name="province" class="form-control" required>
                                @error('province')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Postal Code <span class="text-danger">*</span></label>
                                <input type="text" name="postal_code" class="form-control" required>
                                @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <select name="country_code" class="form-control" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" {{ $country->code == 'RW' ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Fax Number</label>
                                <input type="text" name="fax_number" class="form-control">
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Fax Extension</label>
                                <input type="text" name="fax_ext" class="form-control">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-2"></i> Create Contact
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Handle modal form submission
    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Creating...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
                
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.querySelector('.content').prepend(alert);

                // Reset form
                form.reset();

                // Refresh the page to update contact lists
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to create contact');
            }
        } catch (error) {
            console.error('Error:', error);
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger';
            alert.textContent = error.message;
            form.prepend(alert);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
</script>
@endpush
