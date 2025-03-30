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
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Registrant Contact <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="registrant_contact_id" class="form-control contact-select" id="registrant_contact_id" required>
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
                                        <div id="registrant-contact-details" class="contact-details mt-3" style="display: none;">
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">Contact Details</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Name:</strong> <span class="contact-name"></span></p>
                                                            <p class="mb-1"><strong>Email:</strong> <span class="contact-email"></span></p>
                                                            <p class="mb-1"><strong>Phone:</strong> <span class="contact-phone"></span></p>
                                                            <p class="mb-1"><strong>Organization:</strong> <span class="contact-org"></span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Address:</strong> <span class="contact-street1"></span></p>
                                                            <p class="mb-1"><strong>City:</strong> <span class="contact-city"></span></p>
                                                            <p class="mb-1"><strong>Province:</strong> <span class="contact-province"></span></p>
                                                            <p class="mb-1"><strong>Country:</strong> <span class="contact-country"></span></p>
                                                            <p class="mb-1"><strong>Postal Code:</strong> <span class="contact-postal"></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Please select a registrant contact</div>
                                    </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label font-weight-bold">Admin Contact <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="admin_contact_id" class="form-control contact-select" id="admin_contact_id" required>
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
                                        <div id="admin-contact-details" class="contact-details mt-3" style="display: none;">
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">Contact Details</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Name:</strong> <span class="contact-name"></span></p>
                                                            <p class="mb-1"><strong>Email:</strong> <span class="contact-email"></span></p>
                                                            <p class="mb-1"><strong>Phone:</strong> <span class="contact-phone"></span></p>
                                                            <p class="mb-1"><strong>Organization:</strong> <span class="contact-org"></span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Address:</strong> <span class="contact-street1"></span></p>
                                                            <p class="mb-1"><strong>City:</strong> <span class="contact-city"></span></p>
                                                            <p class="mb-1"><strong>Province:</strong> <span class="contact-province"></span></p>
                                                            <p class="mb-1"><strong>Country:</strong> <span class="contact-country"></span></p>
                                                            <p class="mb-1"><strong>Postal Code:</strong> <span class="contact-postal"></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Please select an admin contact</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold">Technical Contact <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="tech_contact_id" class="form-control contact-select" id="tech_contact_id" required>
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
                                            <div id="tech-contact-details" class="contact-details mt-3" style="display: none;">
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Contact Details</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Name:</strong> <span class="contact-name"></span></p>
                                                                <p class="mb-1"><strong>Email:</strong> <span class="contact-email"></span></p>
                                                                <p class="mb-1"><strong>Phone:</strong> <span class="contact-phone"></span></p>
                                                                <p class="mb-1"><strong>Organization:</strong> <span class="contact-org"></span></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Address:</strong> <span class="contact-street1"></span></p>
                                                                <p class="mb-1"><strong>City:</strong> <span class="contact-city"></span></p>
                                                                <p class="mb-1"><strong>Province:</strong> <span class="contact-province"></span></p>
                                                                <p class="mb-1"><strong>Country:</strong> <span class="contact-country"></span></p>
                                                                <p class="mb-1"><strong>Postal Code:</strong> <span class="contact-postal"></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Please select a technical contact</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold">Billing Contact <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="billing_contact_id" class="form-control contact-select" id="billing_contact_id" required>
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
                                            <div id="billing-contact-details" class="contact-details mt-3" style="display: none;">
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Contact Details</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Name:</strong> <span class="contact-name"></span></p>
                                                                <p class="mb-1"><strong>Email:</strong> <span class="contact-email"></span></p>
                                                                <p class="mb-1"><strong>Phone:</strong> <span class="contact-phone"></span></p>
                                                                <p class="mb-1"><strong>Organization:</strong> <span class="contact-org"></span></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1"><strong>Address:</strong> <span class="contact-street1"></span></p>
                                                                <p class="mb-1"><strong>City:</strong> <span class="contact-city"></span></p>
                                                                <p class="mb-1"><strong>Province:</strong> <span class="contact-province"></span></p>
                                                                <p class="mb-1"><strong>Country:</strong> <span class="contact-country"></span></p>
                                                                <p class="mb-1"><strong>Postal Code:</strong> <span class="contact-postal"></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
$(document).ready(function() {
    // Store all contacts data
    let existingContacts = {!! json_encode($existingContacts ?? []) !!};
    console.log('Existing contacts loaded:', existingContacts);

    // Fetch all contacts from the server to ensure we have complete data
    $.ajax({
        url: window.location.origin + '/api/user/contacts',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success && data.contacts) {
                console.log('Fetched contacts from server:', data.contacts);
                // Update our local contacts data
                const contactsByType = {};
                const contactTypes = ['registrant', 'admin', 'tech', 'billing'];

                // Initialize contact types
                contactTypes.forEach(type => {
                    contactsByType[type] = data.contacts;
                });

                existingContacts = contactsByType;

                // Update all contact dropdowns with the new data
                updateContactDropdowns();
            }
        }
    });

    // Function to update all contact dropdowns with the latest data
    function updateContactDropdowns() {
        const contactTypes = ['registrant', 'admin', 'tech', 'billing'];

        contactTypes.forEach(type => {
            const dropdown = $('#' + type + '_contact_id');
            const selectedValue = dropdown.val();

            // Clear existing options except the first one
            dropdown.find('option:not(:first)').remove();

            // Add options from our contacts data
            if (existingContacts[type] && existingContacts[type].length > 0) {
                existingContacts[type].forEach(contact => {
                    dropdown.append(new Option(
                        contact.name + ' (' + contact.email + ')',
                        contact.id
                    ));
                });

                // Restore selected value if it exists
                if (selectedValue) {
                    dropdown.val(selectedValue);
                }
            }
        });
    }

    // Initialize contact details display
    function updateContactDetails(type) {
        const contactId = $('#' + type + '_contact_id').val();
        const detailsDiv = $('#' + type + '-contact-details');

        if (contactId) {
            console.log('Selected contact ID for ' + type + ':', contactId);

            // Find the contact in our local data
            const contacts = existingContacts[type] || [];
            const contact = contacts.find(c => parseInt(c.id) === parseInt(contactId));

            if (contact) {
                console.log('Found contact in local data:', contact);
                displayContactDetails(detailsDiv, contact);

                // If we're missing some details, try to fetch them from the server
                if (!contact.street1 || !contact.city) {
                    console.log('Fetching additional contact details from server');
                    $.ajax({
                        url: window.location.origin + '/api/contacts/' + contactId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                console.log('Received additional contact details:', data.contact);
                                // Update the contact in our local data
                                Object.assign(contact, data.contact);
                                // Update the display with the new data
                                displayContactDetails(detailsDiv, contact);
                            }
                        }
                    });
                }
            } else {
                console.error('Contact not found in local data');
                detailsDiv.hide();
            }
        } else {
            detailsDiv.hide();
        }
    }

    // Helper function to display contact details
    function displayContactDetails(detailsDiv, contact) {
        console.log('Displaying contact details:', contact);

        // Basic info
        detailsDiv.find('.contact-name').text(contact.name || 'N/A');
        detailsDiv.find('.contact-email').text(contact.email || 'N/A');
        detailsDiv.find('.contact-phone').text(contact.voice || 'N/A');
        detailsDiv.find('.contact-org').text(contact.organization || 'N/A');

        // Address info
        detailsDiv.find('.contact-street1').text(contact.street1 || 'N/A');
        detailsDiv.find('.contact-city').text(contact.city || 'N/A');
        detailsDiv.find('.contact-province').text(contact.province || 'N/A');
        detailsDiv.find('.contact-country').text(contact.country_code || 'N/A');
        detailsDiv.find('.contact-postal').text(contact.postal_code || 'N/A');

        // Make sure the details are visible
        detailsDiv.show();

        // Scroll to make sure the details are visible if needed
        if (!isElementInViewport(detailsDiv[0])) {
            $('html, body').animate({
                scrollTop: detailsDiv.offset().top - 100
            }, 500);
        }
    }

    // Helper function to check if an element is in the viewport
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Handle contact selection change
    $('.contact-select').on('change', function() {
        const type = $(this).attr('id').replace('_contact_id', '');
        updateContactDetails(type);
    });

    // Handle add new contact button click
    $('.add-contact-btn').on('click', function() {
        const contactType = $(this).data('type');
        $('#contactTypeInput').val(contactType);
        $('#contactTypeDisplay').text(contactType.charAt(0).toUpperCase() + contactType.slice(1));
    });

    // Handle contact form submission
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);
        const contactType = $('#contactTypeInput').val();

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass"></i> Creating...');

        // Submit the form via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    // Add the new contact to the dropdown
                    const newContact = data.contact;
                    const option = new Option(newContact.name + ' (' + newContact.email + ')', newContact.id);
                    $('#' + contactType + '_contact_id').append(option).val(newContact.id).trigger('change');

                    // Show success message
                    const alertDiv = $('<div class="alert alert-success"></div>')
                        .text(data.message || 'Contact created successfully');
                    $('.content').prepend(alertDiv);

                    // Auto-remove the alert after 5 seconds
                    setTimeout(function() {
                        alertDiv.fadeOut('slow', function() { $(this).remove(); });
                    }, 5000);

                    // Close the modal and reset the form
                    $('#contactModal').modal('hide');
                    $('#contactForm')[0].reset();
                } else {
                    // Show error message
                    const errorDiv = $('<div class="alert alert-danger"></div>')
                        .text(data.message || 'Failed to create contact');
                    $('#contactForm').prepend(errorDiv);

                    // Auto-remove the error after 5 seconds
                    setTimeout(function() {
                        errorDiv.fadeOut('slow', function() { $(this).remove(); });
                    }, 5000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                // Show error message
                const errorDiv = $('<div class="alert alert-danger"></div>')
                    .text('An error occurred while creating the contact');
                $('#contactForm').prepend(errorDiv);

                // Auto-remove the error after 5 seconds
                setTimeout(function() {
                    errorDiv.fadeOut('slow', function() { $(this).remove(); });
                }, 5000);
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });

    // Handle domain registration form submission
    $('#domainRegistrationForm').on('submit', function(e) {
        e.preventDefault();

        // Validate contacts are selected
        let valid = true;
        const contactTypes = ['registrant', 'admin', 'tech', 'billing'];

        contactTypes.forEach(function(type) {
            const select = $('#' + type + '_contact_id');
            if (!select.val()) {
                select.addClass('is-invalid');
                valid = false;
            } else {
                select.removeClass('is-invalid');
            }
        });

        if (valid) {
            // Show loading state
            const submitBtn = $('#registerDomainBtn');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="bi bi-hourglass"></i> Registering Domain...');

            // Submit the form
            this.submit();
        } else {
            // Scroll to the first error
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }

            // Show error message
            const errorDiv = $('<div class="alert alert-danger"></div>')
                .text('Please select all required contacts');
            $('.content-header').after(errorDiv);

            // Auto-remove the error after 5 seconds
            setTimeout(function() {
                errorDiv.fadeOut('slow', function() { $(this).remove(); });
            }, 5000);
        }
    });

    // Initialize all contact details
    ['registrant', 'admin', 'tech', 'billing'].forEach(function(type) {
        updateContactDetails(type);
    });
});
</script>
@endpush
