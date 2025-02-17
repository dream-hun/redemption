<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Update Nameservers for {{ $domain->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('client.domains.update-nameservers', $domain) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Nameserver Configuration</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Enter at least 2 nameservers. You can add up to 13 nameservers.
                                </p>
                            </div>

                            <div class="space-y-4" id="nameservers-container">
                                @foreach($domain->nameservers ?? [] as $index => $nameserver)
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-grow">
                                            <label for="nameserver{{ $index + 1 }}" class="block text-sm font-medium text-gray-700">
                                                Nameserver {{ $index + 1 }}
                                            </label>
                                            <input type="text" name="nameservers[]" id="nameserver{{ $index + 1 }}"
                                                value="{{ old('nameservers.'.$index, $nameserver) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="ns1.example.com">
                                        </div>
                                        @if($index > 1)
                                            <button type="button" onclick="removeNameserver(this)"
                                                class="mt-6 inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @error('nameservers')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @error('nameservers.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <button type="button" onclick="addNameserver()"
                                class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Add Nameserver
                            </button>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('client.domains.show', $domain) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Nameservers
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addNameserver() {
            const container = document.getElementById('nameservers-container');
            const count = container.children.length;
            
            if (count >= 13) {
                alert('Maximum 13 nameservers allowed');
                return;
            }

            const div = document.createElement('div');
            div.className = 'flex items-center space-x-4';
            div.innerHTML = `
                <div class="flex-grow">
                    <label for="nameserver${count + 1}" class="block text-sm font-medium text-gray-700">
                        Nameserver ${count + 1}
                    </label>
                    <input type="text" name="nameservers[]" id="nameserver${count + 1}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="ns1.example.com">
                </div>
                <button type="button" onclick="removeNameserver(this)"
                    class="mt-6 inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;

            container.appendChild(div);
        }

        function removeNameserver(button) {
            const container = document.getElementById('nameservers-container');
            if (container.children.length > 2) {
                button.closest('.flex').remove();
                // Update labels
                container.querySelectorAll('label').forEach((label, index) => {
                    label.textContent = `Nameserver ${index + 1}`;
                });
            } else {
                alert('Minimum 2 nameservers required');
            }
        }
    </script>
</x-app-layout>
