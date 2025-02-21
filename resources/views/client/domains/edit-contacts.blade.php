<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Update Contacts for {{ $domain->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('client.domains.update-contacts', $domain) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Contact Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    This information will be used for all contact types (Registrant, Administrative, and Technical).
                                </p>
                            </div>

                            <div class="grid grid-cols-6 gap-6">
                                <!-- Name -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="contact_info[name]" id="name"
                                        value="{{ old('contact_info.name', $domain->registrantContact?->name) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Organization -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="organization" class="block text-sm font-medium text-gray-700">Organization</label>
                                    <input type="text" name="contact_info[organization]" id="organization"
                                        value="{{ old('contact_info.organization', $domain->registrantContact?->organization) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.organization')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Street Address 1 -->
                                <div class="col-span-6">
                                    <label for="street1" class="block text-sm font-medium text-gray-700">Street Address</label>
                                    <input type="text" name="contact_info[streets][]" id="street1"
                                        value="{{ old('contact_info.streets.0', $domain->registrantContact?->street1) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.streets.0')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Street Address 2 -->
                                <div class="col-span-6">
                                    <label for="street2" class="block text-sm font-medium text-gray-700">Street Address Line 2</label>
                                    <input type="text" name="contact_info[streets][]" id="street2"
                                        value="{{ old('contact_info.streets.1', $domain->registrantContact?->street2) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.streets.1')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- City -->
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="contact_info[city]" id="city"
                                        value="{{ old('contact_info.city', $domain->registrantContact?->city) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Province/State -->
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="province" class="block text-sm font-medium text-gray-700">Province/State</label>
                                    <input type="text" name="contact_info[province]" id="province"
                                        value="{{ old('contact_info.province', $domain->registrantContact?->province) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.province')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Postal Code -->
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                                    <input type="text" name="contact_info[postal_code]" id="postal_code"
                                        value="{{ old('contact_info.postal_code', $domain->registrantContact?->postal_code) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.postal_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Country -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="country_code" class="block text-sm font-medium text-gray-700">Country</label>
                                    <select name="contact_info[country_code]" id="country_code"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @foreach($countries as $country)
                                            <option value="{{ $country->code }}"
                                                {{ old('contact_info.country_code', $domain->registrantContact?->country_code) == $country->code ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('contact_info.country_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="voice" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="tel" name="contact_info[voice]" id="voice"
                                        value="{{ old('contact_info.voice', $domain->registrantContact?->voice) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.voice')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-span-6">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="contact_info[email]" id="email"
                                        value="{{ old('contact_info.email', $domain->registrantContact?->email) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_info.email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('client.domains.show', $domain) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Contacts
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
