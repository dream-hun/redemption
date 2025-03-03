@extends('layouts.front')
@section('content')
        <div class="max-w-7xl mx-auto">
            <div class="mx-auto max-w-2xl px-4 pb-24 pt-16 sm:px-6 lg:max-w-7xl lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Shopping Cart</h1>
                <form class="mt-12 lg:grid lg:grid-cols-12 lg:items-start lg:gap-x-12 xl:gap-x-16">
                    <section aria-labelledby="cart-heading"
                        class="lg:col-span-7 rounded-lg bg-gray-50 px-4 py-6 sm:p-6 lg:mt-0 lg:p-8">
                        <h2 id="cart-heading" class="sr-only">Items in your shopping cart</h2>

                        <ul role="list" class="divide-y divide-gray-200 border-b border-gray-200">



                            @foreach ($items as $item)
                                <li class="flex py-6 sm:py-10" data-item-id="{{ $item->uuid }}">
                                    <div class="ml-4 flex flex-1 flex-col justify-between sm:ml-6">
                                        <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                            <div>
                                                <div class="flex justify-between">
                                                    <h3 class="text-md">
                                                        <a href="#"
                                                            class="font-medium text-gray-700 hover:text-gray-800">{{ $item->domain }}</a>
                                                    </h3>

                                                </div>

                                                <p class="mt-1 text-md font-bold text-gray-900">
                                                    {{ Cknow\Money\Money::RWF($item->price) }} / Year
                                                </p>
                                            </div>

                                            <div class="mt-4 sm:mt-0 sm:pr-9">
                                                <label for="period-{{ $item->uuid }}" class="sr-only">Registration
                                                    Period</label>
                                                <select id="period-{{ $item->uuid }}" name="period"
                                                    class="period-select max-w-full rounded-md border border-gray-300 py-1.5 text-left text-base font-medium leading-5 text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm"
                                                    data-item-id="{{ $item->uuid }}">
                                                    @for ($i = 1; $i <= 10; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $item->period == $i ? 'selected' : '' }}>
                                                            {{ $i }} {{ Str::plural('Year', $i) }}
                                                        </option>
                                                    @endfor
                                                </select>

                                                <div class="absolute right-0 top-0">
                                                    <button type="button"
                                                        class="remove-item -m-2 inline-flex p-2 text-gray-400 hover:text-gray-500"
                                                        data-item-id="{{ $item->uuid }}">
                                                        <span class="sr-only">Remove</span>
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="mt-4 flex space-x-2 text-sm text-gray-700">
                                            <svg class="h-5 w-5 flex-shrink-0 text-green-500" viewBox="0 0 20 20"
                                                fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Available</span>
                                        </p>
                                    </div>
                                </li>
                            @endforeach


                        </ul>
                    </section>

                    <!-- Order summary -->
                    <section aria-labelledby="summary-heading"
                        class="mt-16 rounded-lg bg-gray-50 px-4 py-6 sm:p-6 lg:col-span-5 lg:mt-0 lg:p-8">
                        <h2 id="summary-heading" class="text-lg font-bold text-gray-900">Order summary</h2>

                        <dl class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600">Subtotal</dt>
                                <dd class="text-sm font-medium text-gray-900" data-subtotal>
                                    {{ Cknow\Money\Money::RWF($items->sum(function ($item) {return $item->price * $item->period;}))->format() }}
                                </dd>
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                <dt class="flex text-sm text-gray-600">
                                    <span>VAT Tax (18%)</span>
                                    <a href="#" class="ml-2 flex-shrink-0 text-gray-400 hover:text-gray-500">
                                        <span class="sr-only">Learn more about how tax is calculated</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM8.94 6.94a.75.75 0 00-1.061-1.061 3 3 0 112.871 5.026v.345a.75.75 0 01-1.5 0v-.5c0-.72.57-1.172 1.081-1.287A1.5 1.5 0 108.94 6.94zM10 15a1 1 0 100-2 1 1 0 000 2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </dt>
                                <dd class="text-sm font-medium text-gray-900" data-tax>
                                    {{ Cknow\Money\Money::RWF($items->sum(function ($item) {return $item->price * $item->period * 0.18;}))->format() }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                <dt class="text-base font-medium text-gray-900">Order total</dt>
                                <dd class="text-base font-medium text-gray-900" data-total>
                                    {{ Cknow\Money\Money::RWF($items->sum(function ($item) {return $item->price * $item->period * 1.18;}))->format() }}
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <a href="{{ route('contacts.create') }}"
                                class="w-full rounded-md border border-transparent bg-blue-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-50">
                                Checkout
                            </a>
                        </div>
                    </section>
                </form>
            </div>



            <!-- Footer -->
            <div class="mt-8 text-center text-gray-500 text-sm">
                <span> {{ date('Y') }} {{ config('app.name') }}</span>
                <a href="#" class="ml-4 text-purple-600">Terms of service</a>
                <a href="#" class="ml-4 text-purple-600">Privacy policy</a>
            </div>
        </div>
    </div>
@endsection


    <script>
        window.addEventListener('load', function() {
            if (typeof jQuery != 'undefined') {
                $(document).ready(function() {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // Function to format price in RWF
                    function formatPrice(price) {
                        return new Intl.NumberFormat('rw-RW', {
                            style: 'currency',
                            currency: 'RWF',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(price);
                    }

                    // Handle period change
                    $('.period-select').on('change', function() {
                        const itemId = $(this).data('item-id');
                        const period = $(this).val();
                        const $item = $(this).closest('li');

                        // Update cart on server
                        $.ajax({
                            url: '/cart/update-period',
                            method: 'POST',
                            data: {
                                item_id: itemId,
                                period: period
                            },
                            success: function(response) {
                                // Refresh the page to show updated data
                                window.location.reload();
                            },
                            error: function(xhr) {
                                console.error('Update failed:', xhr.responseJSON);
                                alert('Failed to update period. Please try again.');
                            }
                        });
                    });

                    // Handle remove item
                    $('.remove-item').on('click', function() {
                        const itemId = $(this).data('item-id');

                        $.ajax({
                            url: '/cart/remove-item',
                            method: 'POST',
                            data: {
                                item_id: itemId
                            },
                            success: function(response) {
                                window.location.reload();
                            },
                            error: function(xhr) {
                                console.error('Remove failed:', xhr.responseJSON);
                                alert('Failed to remove item. Please try again.');
                            }
                        });
                    });
                });
            } else {
                console.error('jQuery is not loaded');
            }
        });
    </script>
</x-app-layout>
