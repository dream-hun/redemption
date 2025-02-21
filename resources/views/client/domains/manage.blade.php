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
                        <a href="#DNS"
                            class="px-2 py-1 border-b-2 font-medium text-sm hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab" aria-controls="DNS" aria-selected="true">DNS</a>

                        <a href="#contact"
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab" aria-controls="contact" aria-selected="false">Contact</a>

                        <a href="#renewal"
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab" aria-controls="renewal" aria-selected="false">Renewal</a>

                        <a href="#deletion"
                            class="px-2 py-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:border-gray-700"
                            role="tab" aria-controls="deletion" aria-selected="false">Deletion</a>
                    </nav>
                </div>
                <div class="p-6">
                    <div id="DNS" class="tab-pane fade show active" role="tabpanel" aria-labelledby="DNS-tab">
                        Update Dns
                    </div>
                    <div id="contact" class="tab-pane fade" role="tabpanel" aria-labelledby="contact-tab">
                        Update contacts
                    </div>
                    <div id="renewal" class="tab-pane fade" role="tabpanel" aria-labelledby="renewal-tab">
                        Renew Domain
                    </div>
                    <div id="deletion" class="tab-pane fade" role="tabpanel" aria-labelledby="deletion-tab">
                        Delete Domain
                    </div>
                </div>

            </div>
        </div>
</x-app-layout>
