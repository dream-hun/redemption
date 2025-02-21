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
                            aria-selected="true">DNS</button>

                        <button
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab" 
                            data-tab="contact" 
                            aria-controls="contact" 
                            aria-selected="false">Contact</button>

                        <button
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab" 
                            data-tab="renewal" 
                            aria-controls="renewal" 
                            aria-selected="false">Renewal</button>

                        <button
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab" 
                            data-tab="deletion" 
                            aria-controls="deletion" 
                            aria-selected="false">Deletion</button>
                    </nav>
                </div>
                <div class="p-6">
                    <div id="DNS" class="tab-content block" role="tabpanel" aria-labelledby="DNS-tab">
                        Update Dns
                    </div>
                    <div id="contact" class="tab-content hidden" role="tabpanel" aria-labelledby="contact-tab">
                        Update contacts
                    </div>
                    <div id="renewal" class="tab-content hidden" role="tabpanel" aria-labelledby="renewal-tab">
                        Renew Domain
                    </div>
                    <div id="deletion" class="tab-content hidden" role="tabpanel" aria-labelledby="deletion-tab">
                        Delete Domain
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
