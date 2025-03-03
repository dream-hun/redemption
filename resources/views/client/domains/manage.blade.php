<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage {{ $domain->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <nav class="flex space-x-4" role="tablist">
                        <button
                            class="px-2 py-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600"
                            role="tab"
                            data-tab="DNS"
                            aria-controls="DNS"
                            aria-selected="true">Manage Domain DNS</button>

                        <button
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab"
                            data-tab="contact"
                            aria-controls="contact"
                            aria-selected="false">Update Domain Contact</button>

                        <button
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab"
                            data-tab="renewal"
                            aria-controls="renewal"
                            aria-selected="false">Renew Domain</button>

                        <button
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab"
                            data-tab="deletion"
                            aria-controls="deletion"
                            aria-selected="false">Delete Domain</button>
                    </nav>
                </div>
                <div class="p-6">
                    <div id="DNS" class="tab-content block" role="tabpanel" aria-labelledby="DNS-tab">
                        Manage Domain DNS
                    </div>
                    <div id="contact" class="tab-content hidden" role="tabpanel" aria-labelledby="contact-tab">
                        Update Domain Contact
                    </div>
                    <div id="renewal" class="tab-content hidden" role="tabpanel" aria-labelledby="renewal-tab">
                        Renew Domain
                    </div>
                    <div id="deletion" class="tab-content hidden" role="tabpanel" aria-labelledby="deletion-tab">
                        <div class="max-w-xl">
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            <strong class="font-medium">Warning!</strong> This action cannot be undone. This will permanently delete the domain {{ $domain->name }} and all its associated data.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('client.domains.destroy', $domain) }}" class="space-y-6" id="deleteDomainForm">
                                @csrf
                                @method('DELETE')

                                <div>
                                    <x-input-label for="password" value="Please enter your password to confirm deletion" class="text-red-600" />
                                    <x-text-input 
                                        id="password" 
                                        type="password" 
                                        name="password" 
                                        class="mt-1 block w-full" 
                                        required 
                                        autocomplete="current-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div class="flex items-center gap-4">
                                    <x-danger-button>
                                        Delete Domain
                                    </x-danger-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const tabs = document.querySelectorAll('[role="tab"]');

                        function switchTab(e) {
                            e.preventDefault();

                            // Remove active states from all tabs
                            tabs.forEach(tab => {
                                tab.classList.remove('border-blue-500', 'text-blue-600');
                                tab.classList.add('border-transparent', 'text-gray-500');
                                tab.setAttribute('aria-selected', 'false');
                            });

                            // Add active state to clicked tab
                            e.currentTarget.classList.remove('border-transparent', 'text-gray-500');
                            e.currentTarget.classList.add('border-blue-500', 'text-blue-600');
                            e.currentTarget.setAttribute('aria-selected', 'true');

                            // Hide all tab content
                            document.querySelectorAll('.tab-content').forEach(content => {
                                content.classList.add('hidden');
                                content.classList.remove('block');
                            });

                            // Show selected tab content
                            const targetId = e.currentTarget.getAttribute('data-tab');
                            const targetContent = document.getElementById(targetId);
                            targetContent.classList.remove('hidden');
                            targetContent.classList.add('block');
                        }

                        // Add click event to all tabs
                        tabs.forEach(tab => {
                            tab.addEventListener('click', switchTab);
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
