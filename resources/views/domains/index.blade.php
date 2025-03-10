@extends('layouts.user')
@section('content')
<section class="rts-hero-three rts-hero__one rts-hosting-banner domain-checker-padding banner-default-height">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="rts-hero__content domain">
                    <h1 data-sal="slide-down" data-sal-delay="100" data-sal-duration="800" class="sal-animate">
                        Find
                        Best Unique Domains
                        Checker!
                    </h1>
                    <p class="description sal-animate" data-sal="slide-down" data-sal-delay="200"
                        data-sal-duration="800">Web
                        Hosting, Domain Name and Hosting Center Solutions</p>
                    <form id="domainForm" action="{{ route('domain.check') }}" method="POST" data-sal-delay="300"
                        data-sal-duration="800">
                        @csrf
                        <div class="rts-hero__form-area">

                            <input type="text" placeholder="Type your domain without extension Ex: jhonsmith"
                                name="domains" id="domainText" autocomplete="off">
                            <div class="select-button-area">
                                <select name="extension" id="domainExtension" class="form-select">
                                    @foreach ($tlds as $tld)
                                        <option value="{{ $tld->tld }}">{{ $tld->tld }}</option>
                                    @endforeach
                                </select>
                                <button id="checkButton" type="submit">Search</button>
                            </div>
                        </div>
                    </form>
                    <div class="banner-content-tag" data-sal-delay="400" data-sal-duration="800">
                        <p class="desc">Popular Domain:</p>
                        <ul class="tag-list">
                            @foreach ($tlds as $tld)
                                <li><span>{{ $tld->tld }}</span><span>{{ $tld->formatedRegisterPrice() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="banner-shape-area">
        <img class="three" src="assets/images/banner/banner-bg-element.svg" alt="">
    </div>
</section>
<section class="rts-domain-pricing-area pt--120 pb--120">
    <div class="container">

        <div class="row justify-content-center">
            <div class="section-title-area w-570">
                <h2 class="section-title sal-animate" data-sal="slide-down" data-sal-delay="100"
                    data-sal-duration="800">Choose the domain that suits your busines or organisation</h2>
                <p class="desc sal-animate" data-sal="slide-down" data-sal-delay="200" data-sal-duration="800">
                    Straightforward
                    Domain Pricing</p>
            </div>
        </div>
        <div class="section-inner" id="results">
            <div class="row g-5">
                <div id="errorMessage" class="alert alert-danger hidden"></div>
                <div id="statusMessage" class="alert alert-info hidden"></div>
                <div id="resultsContainer" class="col-lg-12">
                    <!-- Results will be appended here -->
                </div>
            </div>
        </div>
    </div>
</section>

@endsection


@section('scripts')
    <script>
        var domainCheckRoute = "{{ route('domain.check') }}";
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initially hide the messages
            $('#errorMessage, #statusMessage').hide();

            // Handle domain check form submission
            $('#domainForm').on('submit', function(e) {
                e.preventDefault();

                const $button = $('#checkButton');
                const $results = $('#results');
                const $resultsContainer = $('#resultsContainer');
                const $errorMessage = $('#errorMessage');
                const $statusMessage = $('#statusMessage');

                // Clear previous results and messages
                $results.hide();
                $errorMessage.hide();
                $statusMessage.hide();
                $resultsContainer.empty();

                // Get and validate domain input
                const domain = $('#domainText').val().trim().toLowerCase();
                if (!domain) {
                    $errorMessage.text('Please enter a domain name.').show();
                    return;
                }

                // Basic domain name validation
                const domainRegex = /^[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/;
                if (!domainRegex.test(domain)) {
                    $errorMessage.text(
                        'Please enter a valid domain name (letters, numbers, and hyphens only, cannot start or end with hyphen).'
                        ).show();
                    return;
                }

                // Show searching status
                $statusMessage.text('Searching for domains...').show();

                // Disable button and show loading state
                $button.prop('disabled', true).text('Checking...');

                // Perform domain check
                $.ajax({
                    url: "{{ route('domain.check') }}",
                    method: 'POST',
                    data: {
                        domains: domain,
                        extension: $('#domainExtension').val()
                    },
                    success: function(response) {
                        $results.show();
                        $statusMessage.hide();

                        if (response.error) {
                            $errorMessage.html(`<strong>Error:</strong> ${response.error}`)
                                .show();
                            if (response.message) {
                                $errorMessage.append(`<br><small>${response.message}</small>`);
                            }
                        } else if (response.results) {
                            const resultCount = Object.keys(response.results).length;
                            if (resultCount === 0) {
                                $statusMessage.text('No domain results found.').show();
                            } else {
                                $resultsContainer.empty(); // Clear previous results

                                Object.entries(response.results).forEach(([domain, result]) => {
                                    console.log('Processing domain result:', {
                                        domain,
                                        result
                                    }); // Debug log

                                    const availabilityClass = result.available ?
                                        'available' : 'unavailable';
                                    const availabilityText = result.available ?
                                        'Available!' : 'Not Available';
                                    const reasonText = result.reason ?
                                        `<br><small>${result.reason}</small>` : '';

                                    const resultHtml = `
                                        <div class="col-lg-4 col-xl-3 col-md-3 col-sm-6 sal-animate" data-sal="slide-down" data-sal-delay="200" data-sal-duration="800">
                                            <div class="pricing-wrapper ${availabilityClass}">
                                                <div class="logo"><img src="assets/images/pricing/domain-01.svg" alt=""></div>
                                                <div class="content">
                                                    <p class="desc">
                                                        <strong>${domain}</strong><br>
                                                        <span class="status ${availabilityClass}">${availabilityText}</span>
                                                        ${reasonText}
                                                    </p>
                                                    <div class="price-area">
                                                        ${result.available ? `<span class="now">RWF ${result.register_price}</span>` : ''}
                                                    </div>
                                                    <div class="button-area">
                                                        ${result.available ? `
                                                                <button type="button" class="pricing-btn rts-btn addToCartButton"
                                                                    data-domain="${domain}"
                                                                    data-price="${result.register_price}">
                                                                    Add to Cart
                                                                </button>
                                                            ` : `<p class="unavailable-reason">${result.reason || 'This domain is not available for registration'}</p>`}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    $resultsContainer.append(resultHtml);
                                });

                                // Add some styles for better visibility
                                $('<style>')
                                    .text(`
                                        .pricing-wrapper.available { border: 2px solid #28a745; }
                                        .pricing-wrapper.unavailable { border: 2px solid #dc3545; }
                                        .status.available { color: #28a745; font-weight: bold; }
                                        .status.unavailable { color: #dc3545; font-weight: bold; }
                                        .unavailable-reason { color: #6c757d; font-style: italic; }
                                    `)
                                    .appendTo('head');

                                $resultsContainer.addClass('row g-5');
                            }
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.error ||
                            'An error occurred while checking domains.';
                        const detailMsg = xhr.responseJSON?.message || '';
                        $errorMessage.html(
                            `<strong>Error:</strong> ${errorMsg}${detailMsg ? `<br><small>${detailMsg}</small>` : ''}`
                            ).show();
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('Check Availability');
                    }
                });
            });


            // Handle Add to Cart using event delegation
            $(document).on('click', '.addToCartButton', function() {
                const button = $(this);
                const domain = button.data('domain');
                const price = button.data('price');

                button.prop('disabled', true).text('Adding...');

                $.ajax({
                    url: '/add-to-cart', // STILL INCORRECT - MUST BE NAMED ROUTE
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        domain: domain,
                        price: price
                    },
                    success: function(response) {

                        window.location.href = `/domains`;
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                        alert(xhr.responseJSON ? JSON.stringify(xhr.responseJSON) :
                            'Failed to add to cart.');
                        button.prop('disabled', false).text('Add to Cart');
                    }
                });
            });
        });
    </script>
@endsection
