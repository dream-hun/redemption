<div>
    <style>
        .domain-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        .results-wrapper {
            position: relative;
            min-height: 200px;
        }
        .domain-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-left: 10px;
        }
        .domain-badge.available {
            background-color: #28a745;
            color: white;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
        }
        .domain-badge.unavailable {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
        }
        .cart-button {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 75%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        .cart-button.add {
            color: white;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
        }
        .cart-button.add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(40, 167, 69, 0.3);
        }
        .cart-button.remove {
            color: white;
            box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);
        }
        .cart-button.remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(220, 53, 69, 0.3);
        }
        .cart-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
    </style>

    <section class="rts-hero-three rts-hero__one rts-hosting-banner domain-checker-padding banner-default-height" style="max-height: 450px !important;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="rts-hero__content domain">
                        <h1 data-sal="slide-down" data-sal-delay="100" data-sal-duration="800" class="sal-animate">
                            Find Best Unique Domains Checker!
                        </h1>
                        <p class="description sal-animate" data-sal="slide-down" data-sal-delay="200"
                            data-sal-duration="800">
                            Web Hosting, Domain Name and Hosting Center Solutions
                        </p>

                        <form wire:submit.prevent="search" id="domainForm" data-sal-delay="300" data-sal-duration="800">
                            <div class="rts-hero__form-area">
                                <input type="text" placeholder="Type your domain without extension Ex: jhonsmith"
                                    wire:model.defer="domain" id="domainText" autocomplete="off"
                                    class="form-select @error('domain') is-invalid @enderror">
                                <div class="select-button-area">
                                    <select wire:model.defer="extension" id="domainExtension">
                                        @foreach ($tlds as $tld)
                                            <option value="{{ $tld->tld }}">{{ $tld->tld }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" id="checkButton" wire:loading.attr="disabled"
                                        wire:target="search">
                                        <span wire:loading.remove wire:target="search">Search</span>
                                        <span wire:loading wire:target="search">Searching...</span>
                                    </button>
                                </div>
                            </div>
                            @error('domain')
                                <div class="domain-search-error">{{ $message }}</div>
                            @enderror
                        </form>
                        <div class="banner-content-tag" data-sal-delay="400" data-sal-duration="800">
                            <p class="desc">Popular Domain:</p>
                            <ul class="tag-list">
                                @foreach ($tlds as $tld)
                                    <li>
                                        <span>{{ $tld->tld }}</span>
                                        <span>{{ Cknow\Money\Money::RWF($tld->register_price)->format() }}</span>
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

    <section class="rts-domain-pricing-area pt--30 pb--70">
        <div class="container">
            <div class="row justify-content-center">
                <div class="section-title-area w-full">

                </div>
            </div>
            <div class="section-inner" id="results" wire:loading.class="domain-search-loading">
                <div class="row g-5">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($error)
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endif

                    <div class="results-wrapper">
                        <!-- Loading Overlay -->
                        <div wire:loading wire:target="search" class="domain-loading-overlay">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="resultsContainer" class="col-lg-12">
                        @if($results && count($results) > 0)
                            @php
                                // Find the primary domain
                                $primaryDomain = null;
                                $primaryResult = null;
                                $suggestedDomains = [];

                                foreach ($results as $domain => $result) {
                                    if ($result['is_primary'] ?? false) {
                                        $primaryDomain = $domain;
                                        $primaryResult = $result;
                                    } else {
                                        $suggestedDomains[$domain] = $result;
                                    }
                                }
                            @endphp

                            @if($primaryDomain && $primaryResult)
                                <!-- Main Requested Domain -->
                                <div class="main-domain-result mb-4 pb--5">
                                    <div class="pricing-wrapper {{ $primaryResult['available'] ? 'available' : 'unavailable' }} p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>{{ $primaryDomain }}</strong></p>
                                                <span class="domain-badge {{ $primaryResult['available'] ? 'available' : 'unavailable' }}">
                                                    {{ $primaryResult['available'] ? 'Available!' : 'Not Available' }}
                                                </span>

                                            </div>
                                            <div class="col-md-3 text-center">
                                                @if ($primaryResult['available'])
                                                    <div class="price-area">
                                                        <span class="now h4">{{ $primaryResult['formatted_price'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-3 text-end">
                                                @if ($primaryResult['available'])
                                                    <button
                                                        wire:click="{{ $primaryResult['in_cart'] ? 'removeFromCart(\'' . $primaryDomain . '\')' : 'addToCart(\'' . $primaryDomain . '\', ' . $primaryResult['register_price'] . ')' }}"
                                                        wire:loading.attr="disabled"
                                                        class="rts-btn btn__long {{ $primaryResult['in_cart'] ? 'btn-danger' : 'btn-success' }} m-auto"
                                                    >
                                                        <span wire:loading.remove>{{ $primaryResult['in_cart'] ? 'Remove from cart' : 'Add to Cart' }}</span>
                                                        <span wire:loading>Loading...</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Suggested Domains -->
                            @if(count($suggestedDomains) > 0)
                                <h4 class="pb--20 pt--20">Suggested Domains</h4>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ($suggestedDomains as $domain => $result)
                                                <tr class="pricing-wrapper {{ $result['available'] ? 'available' : 'unavailable' }}">
                                                    <td class="align-middle" style="width: 40%">
                                                        <strong>{{ $domain }}</strong>
                                                        <div>
                                                            <span class="status {{ $result['available'] ? 'available' : 'unavailable' }}">
                                                                {{ $result['available'] ? 'Available!' : 'Not Available' }}
                                                            </span>

                                                        </div>
                                                    </td>
                                                    <td class="align-middle text-center" style="width: 30%">
                                                        @if ($result['available'])
                                                            <span class="now">{{ $result['formatted_price'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle text-end" style="width: 30%">
                                                        @if ($result['available'])
                                                            <button
                                                                wire:click="{{ $result['in_cart'] ? 'removeFromCart(\'' . $domain . '\')' : 'addToCart(\'' . $domain . '\', ' . $result['register_price'] . ')' }}"
                                                                wire:loading.attr="disabled"
                                                                class="rts-btn btn__long {{ $result['in_cart'] ? 'bg-danger' : 'bg-success' }} w-75"
                                                            >
                                                                <span wire:loading.remove>{{ $result['in_cart'] ? 'Remove from cart' : 'Add to Cart' }}</span>
                                                                <span wire:loading>Loading...</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
