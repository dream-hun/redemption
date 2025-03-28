@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('domain.register') }}" method="POST" id="domainRegistrationForm">

            @csrf
            <input type="hidden" name="domain_name" value="{{ $cartItems->first()->name ?? '' }}">
            <div x-data="domainRegistration">
                <div class="row">
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Domain Contacts</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Registrant Contact <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="registrant_contact_id" class="form-control" x-model="selectedContacts.registrant" required :class="{'is-invalid': errors.registrant}">
                                                <option value="">Select Registrant Contact</option>
                                                <template x-for="contact in existingContacts.registrant" :key="contact.id">
                                                    <option :value="contact.id" x-text="contact.name + ' (' + contact.email + ')'"></option>
                                                </template>
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="contactType = 'registrant'" data-toggle="modal" data-target="#contactModal">
                                                    <i class="bi bi-plus-lg"></i> Add New
                                                </button>
                                            </div>

                                        </div>
                                        <template x-if="selectedContacts.registrant && getSelectedContact('registrant')">
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Name:</strong> <span x-text="getSelectedContact('registrant').name"></span></p>
                                                    <p class="mb-1"><strong>Email:</strong> <span x-text="getSelectedContact('registrant').email"></span></p>
                                                    <p class="mb-1"><strong>Phone:</strong> <span x-text="getSelectedContact('registrant').voice"></span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Organization:</strong> <span x-text="getSelectedContact('registrant').organization || 'N/A'"></span></p>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="invalid-feedback">Please select a registrant contact</div>
                                    </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Admin Contact <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="admin_contact_id" class="form-control" x-model="selectedContacts.admin" required :class="{'is-invalid': errors.admin}">
                                                <option value="">Select Admin Contact</option>
                                                <template x-for="contact in existingContacts.admin" :key="contact.id">
                                                    <option :value="contact.id" x-text="contact.name + ' (' + contact.email + ')'"></option>
                                                </template>
                                            </select>
                                            <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" @click="contactType = 'admin'" data-toggle="modal" data-target="#contactModal">
                                                <i class="bi bi-plus-lg"></i> Add new
                                            </button>
                                            </div>
                                        </div>
                                        <template x-if="selectedContacts.admin && getSelectedContact('admin')">
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Name:</strong> <span x-text="getSelectedContact('admin').name"></span></p>
                                                    <p class="mb-1"><strong>Email:</strong> <span x-text="getSelectedContact('admin').email"></span></p>
                                                    <p class="mb-1"><strong>Phone:</strong> <span x-text="getSelectedContact('admin').voice"></span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Organization:</strong> <span x-text="getSelectedContact('admin').organization || 'N/A'"></span></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="invalid-feedback">Please select an admin contact</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold">Technical Contact <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="tech_contact_id" class="form-control" x-model="selectedContacts.tech" required :class="{'is-invalid': errors.tech}">
                                                    <option value="">Select Technical Contact</option>
                                                    <template x-for="contact in existingContacts.tech" :key="contact.id">
                                                        <option :value="contact.id" x-text="contact.name + ' (' + contact.email + ')'"></option>
                                                    </template>
                                                </select>
                                                <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="contactType = 'tech'" data-toggle="modal" data-target="#contactModal">
                                                    <i class="bi bi-plus-lg"></i> Add new
                                                </button>
                                                </div>
                                            </div>
                                            <template x-if="selectedContacts.tech && getSelectedContact('tech')">
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Name:</strong> <span x-text="getSelectedContact('tech').name"></span></p>
                                                        <p class="mb-1"><strong>Email:</strong> <span x-text="getSelectedContact('tech').email"></span></p>
                                                        <p class="mb-1"><strong>Phone:</strong> <span x-text="getSelectedContact('tech').voice"></span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Organization:</strong> <span x-text="getSelectedContact('tech').organization || 'N/A'"></span></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="invalid-feedback">Please select a technical contact</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold">Billing Contact <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="billing_contact_id" class="form-control" x-model="selectedContacts.billing" required :class="{'is-invalid': errors.billing}">
                                                    <option value="">Select Billing Contact</option>
                                                    <template x-for="contact in existingContacts.billing" :key="contact.id">
                                                        <option :value="contact.id" x-text="contact.name + ' (' + contact.email + ')'"></option>
                                                    </template>
                                                </select>
                                                <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="contactType = 'billing'" data-toggle="modal" data-target="#contactModal">
                                                    <i class="bi bi-plus-lg"></i> Add new
                                                </button>
                                                </div>
                                            </div>
                                            <template x-if="selectedContacts.billing && getSelectedContact('billing')">
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Name:</strong> <span x-text="getSelectedContact('billing').name"></span></p>
                                                        <p class="mb-1"><strong>Email:</strong> <span x-text="getSelectedContact('billing').email"></span></p>
                                                        <p class="mb-1"><strong>Phone:</strong> <span x-text="getSelectedContact('billing').voice"></span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Organization:</strong> <span x-text="getSelectedContact('billing').organization || 'N/A'"></span></p>
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
                                    <input type="text" name="nameservers[0]" class="form-control" placeholder="ns1.example.com" x-model="nameservers[0]">
                                </div>

                                <div class="form-group mb-2">
                                    <label class="form-label">Name Server 2</label>
                                    <input type="text" name="nameservers[1]" class="form-control" placeholder="ns2.example.com" x-model="nameservers[1]">
                                </div>

                                <div class="form-group mb-2">
                                    <label class="form-label">Name Server 3</label>
                                    <input type="text" name="nameservers[2]" class="form-control" placeholder="ns3.example.com" x-model="nameservers[2]">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label">Name Server 4</label>
                                    <input type="text" name="nameservers[3]" class="form-control" placeholder="ns4.example.com" x-model="nameservers[3]">
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
                    <h5 class="modal-title" id="contactModalLabel">Create New <span x-text="contactType.charAt(0).toUpperCase() + contactType.slice(1)"></span> Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('domain.contact.create') }}" method="POST" id="contactForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="contact_type" x-bind:value="contactType">
                            <div class="form-group mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Organization</label>
                                <input type="text" name="organization" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" name="voice" class="form-control" required>
                                @error('voice')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('domainRegistration', () => ({
            debug: true,
            showContactForm: false,
            contactType: '',
            selectedContacts: {
                registrant: null,
                admin: null,
                tech: null,
                billing: null
            },
            nameservers: ['', '', '', ''],
            errors: {
                registrant: false,
                admin: false,
                tech: false,
                billing: false
            },
            existingContacts: {!! json_encode($existingContacts ?? []) !!},
            getSelectedContact(type) {
                const contactId = this.selectedContacts[type];
                return this.existingContacts[type]?.find(c => c.id == contactId) || null;
            },
            init() {
                // Initialize with empty arrays if existingContacts is not defined properly
                if (!this.existingContacts) {
                    this.existingContacts = { registrant: [], admin: [], tech: [], billing: [] };
                }

                // Ensure all contact types exist
                ['registrant', 'admin', 'tech', 'billing'].forEach(type => {
                    if (!this.existingContacts[type]) {
                        this.existingContacts[type] = [];
                    }
                });

                // Modal is now handled by Bootstrap

                // Set up contact form submission
                const contactForm = document.getElementById('contactForm');
                if (contactForm) {
                    contactForm.addEventListener('submit', (e) => {
                        e.preventDefault();

                        // Submit the form via AJAX
                        const formData = new FormData(contactForm);

                        // Show loading state
                        const submitBtn = contactForm.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Creating...';

                        fetch(contactForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Add the new contact to the list
                                const newContact = data.contact;
                                const type = this.contactType;

                                if (!this.existingContacts[type]) {
                                    this.existingContacts[type] = [];
                                }

                                this.existingContacts[type].push(newContact);

                                // Select the new contact
                                this.selectedContacts[type] = newContact.id;

                                // Show success message
                                const alertDiv = document.createElement('div');
                                alertDiv.className = 'alert alert-success';
                                alertDiv.textContent = data.message || 'Contact created successfully';
                                document.querySelector('.content').prepend(alertDiv);

                                // Auto-remove the alert after 5 seconds
                                setTimeout(() => {
                                    alertDiv.remove();
                                }, 5000);

                                // Close the modal
                                $('#contactModal').modal('hide');

                                // Reset the form
                                contactForm.reset();
                            } else {
                                // Show error message
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'alert alert-danger';
                                errorDiv.textContent = data.message || 'Failed to create contact';
                                contactForm.prepend(errorDiv);

                                // Auto-remove the error after 5 seconds
                                setTimeout(() => {
                                    errorDiv.remove();
                                }, 5000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Show error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'alert alert-danger';
                            errorDiv.textContent = 'An error occurred while creating the contact';
                            contactForm.prepend(errorDiv);
                        })
                        .finally(() => {
                            // Reset button state
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                    });
                }

                // Set up domain registration form validation
                const registrationForm = document.getElementById('domainRegistrationForm');
                if (registrationForm) {
                    registrationForm.addEventListener('submit', (e) => {
                        e.preventDefault();

                        // Reset errors
                        this.errors = {
                            registrant: false,
                            admin: false,
                            tech: false,
                            billing: false
                        };

                        // Validate contacts are selected
                        let valid = true;

                        if (!this.selectedContacts.registrant) {
                            this.errors.registrant = true;
                            valid = false;
                        }

                        if (!this.selectedContacts.admin) {
                            this.errors.admin = true;
                            valid = false;
                        }

                        if (!this.selectedContacts.tech) {
                            this.errors.tech = true;
                            valid = false;
                        }

                        if (!this.selectedContacts.billing) {
                            this.errors.billing = true;
                            valid = false;
                        }

                        if (valid) {
                            // Show loading state
                            const submitBtn = document.getElementById('registerDomainBtn');
                            const originalText = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Registering Domain...';

                            // Submit the form
                            registrationForm.submit();
                        } else {
                            // Scroll to the first error
                            const firstError = document.querySelector('.is-invalid');
                            if (firstError) {
                                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }

                            // Show error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'alert alert-danger';
                            errorDiv.textContent = 'Please select all required contacts';
                            document.querySelector('.content-header').after(errorDiv);

                            // Auto-remove the error after 5 seconds
                            setTimeout(() => {
                                errorDiv.remove();
                            }, 5000);
                        }
                    });
                }
            }
        }))
    });
</script>
@endpush
