<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>
    <div class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-6">Domain Contact Information</h2>

                        <form id="contactForm" method="POST" action="{{ route('domains.register') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="contact_info[name]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.name') border-red-500 @enderror"
                                        value="{{ old('contact_info.name', Auth::user()->name) }}" required>
                                    @error('contact_info.name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Organization</label>
                                    <input type="text" name="contact_info[organization]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.organization') border-red-500 @enderror"
                                        value="{{ old('contact_info.organization') }}" required>
                                    @error('contact_info.organization')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Street Address</label>
                                    <div id="streets-container">
                                        <input type="text" name="contact_info[streets][]"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.streets.0') border-red-500 @enderror"
                                            value="{{ old('contact_info.streets.0') }}" required>
                                    </div>
                                    <button type="button" id="addStreet"
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add
                                        another street line</button>
                                    @error('contact_info.streets')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="contact_info[city]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.city') border-red-500 @enderror"
                                        value="{{ old('contact_info.city') }}" required>
                                    @error('contact_info.city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Province/State</label>
                                    <input type="text" name="contact_info[province]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.province') border-red-500 @enderror"
                                        value="{{ old('contact_info.province') }}" required>
                                    @error('contact_info.province')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Postal Code</label>
                                    <input type="text" name="contact_info[postal_code]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.postal_code') border-red-500 @enderror"
                                        value="{{ old('contact_info.postal_code') }}" required>
                                    @error('contact_info.postal_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Country</label>
                                    <select name="contact_info[country_code]" id="country-select"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.country_code') border-red-500 @enderror"
                                        required>
                                        <option value="">Select a country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->code }}"
                                                {{ old('contact_info.country_code') == $country->code ? 'selected' : '' }}>
                                                {{ $country->name }} ({{ $country->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('contact_info.country_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Contact Information -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="tel" name="contact_info[voice]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.voice') border-red-500 @enderror"
                                        value="{{ old('contact_info.voice') }}" required>
                                    @error('contact_info.voice')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Include country code (e.g., +1.2025551234)</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="contact_info[email]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_info.email') border-red-500 @enderror"
                                        value="{{ old('contact_info.email', Auth::user()->email) }}" required>
                                    @error('contact_info.email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="col-span-2">
                                    <button type="submit"
                                        class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Continue to Domain Registration
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h2 class="text-xl font-bold mb-4">Cart Summary</h2>

                        <!-- Domain List -->
                        <div class="space-y-4 mb-6">
                            @foreach ($cartItems as $item)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $item->domain }}</h3>
                                            <p class="text-sm text-gray-600">{{ $item->period }}
                                                {{ Str::plural('year', $item->period) }}</p>
                                        </div>
                                        <p class="text-gray-900">
                                            {{ Cknow\Money\Money::RWF($item->price * $item->period) }}</p>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <p>{{ $item->getBasePrice() }}/year</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Summary Calculations -->
                        <div class="space-y-2 border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-base">
                                <p class="text-gray-600">Subtotal</p>
                                <p class="font-medium text-gray-900"> {{ $item->subTotal() }}
                                </p>
                            </div>
                            <div class="flex justify-between text-base">
                                <p class="text-gray-600">VAT (18%)</p>
                                <p class="font-medium text-gray-900"> {{ $item->getTax() }}</p>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                <p>Total</p>
                                <p> {{ $item->getTotal() }}</p>
                            </div>
                        </div>

                        <!-- Back to Cart Link -->
                        <div class="mt-6 text-center">
                            <a href="{{ route('cart.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                ← Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.getElementById('addStreet').addEventListener('click', function() {
                const container = document.getElementById('streets-container');
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'contact_info[streets][]';
                input.className =
                    'mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';
                input.required = true;
                container.appendChild(input);
            });

            // Initialize Select2 for country selection
            $(document).ready(function() {
                $('#country-select').select2({
                    placeholder: 'Select a country',
                    allowClear: true,
                    width: '100%',
                    theme: 'classic',
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
