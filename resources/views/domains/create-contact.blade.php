<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>
    <div class="py-24">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Domain Contact Information</h2>

            <form id="contactForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contact Type -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Contact Type</label>
                        <select name="contact_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="registrant">Registrant</option>
                            <option value="admin">Administrative</option>
                            <option value="tech">Technical</option>
                        </select>
                    </div>

                    <!-- Basic Information -->

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ Auth::user()->name }}" required>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Organization</label>
                        <input type="text" name="organization"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Address -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Street Address</label>
                        <input type="text" name="streets[]"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <button type="button" id="addStreet" class="mt-2 text-sm text-blue-600 hover:text-blue-800">+
                            Add
                            another street line</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Province/State</label>
                        <input type="text" name="province"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Country Code</label>
                        <input type="text" name="country_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required maxlength="2">
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" name="voice" value="{{ Auth::user()->email }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fax</label>
                        <input type="tel" name="fax"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ Auth::user()->email }}" required>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="resetForm()">Reset</button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Create
                        Contact</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Add additional street address field
            $('#addStreet').click(function() {
                const streetFields = $('input[name="streets[]"]');
                if (streetFields.length < 3) {
                    const newField = $(
                        '<input type="text" name="streets[]" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">'
                    );
                    $(this).before(newField);
                }
                if (streetFields.length >= 2) {
                    $(this).hide();
                }
            });

            // Handle form submission
            $('#contactForm').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                const processedData = processFormData(formData);

                $.ajax({
                    url: '/api/contacts',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(processedData),
                    success: function(response) {
                        alert('Contact created successfully!');
                        resetForm();
                    },
                    error: function(xhr) {
                        alert('Error creating contact: ' + xhr.responseText);
                    }
                });
            });
        });

        function processFormData(formData) {
            const processed = {
                streets: [],
                fax: {
                    number: '',
                    ext: ''
                }
            };

            formData.forEach(item => {
                if (item.name === 'streets[]') {
                    processed.streets.push(item.value);
                } else if (item.name === 'fax') {
                    processed.fax.number = item.value;
                } else {
                    processed[item.name] = item.value;
                }
            });

            return processed;
        }

        function resetForm() {
            $('#contactForm')[0].reset();
            $('input[name="streets[]"]').not(':first').remove();
            $('#addStreet').show();
        }
    </script>
</x-app-layout>
