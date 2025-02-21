<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $domain->name }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('client.domains.edit-contacts', $domain) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Update Contacts
                </a>
                <a href="{{ route('client.domains.edit-nameservers', $domain) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Update Nameservers
                </a>
                <a href="{{ route('client.domains.renew', $domain) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Renew Domain
                </a>
                <form action="{{ route('client.domains.destroy', $domain) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this domain?')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete Domain
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Domain Information -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Domain Information</h3>
                            <dl class="grid grid-cols-2 gap-4">
                                <dt class="font-medium">Status</dt>
                                <dd>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $domain->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($domain->status) }}
                                    </span>
                                </dd>
                                <dt class="font-medium">Registration Date</dt>
                                <dd>{{ $domain->registered_at }}</dd>
                                <dt class="font-medium">Expiry Date</dt>
                                <dd>{{ $domain->expires_at }}</dd>
                                <dt class="font-medium">Last Renewal</dt>
                                <dd>{{ $domain->last_renewal_at ?? 'N/A' }}</dd>
                                <dt class="font-medium">Auto Renew</dt>
                                <dd>{{ $domain->auto_renew ? 'Yes' : 'No' }}</dd>
                                <dt class="font-medium">WHOIS Privacy</dt>
                                <dd>{{ $domain->whois_privacy ? 'Enabled' : 'Disabled' }}</dd>
                            </dl>
                        </div>

                        <!-- Nameservers -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Nameservers</h3>
                            <ul class="space-y-2">
                                @foreach($domain->nameservers as $ns)
                                    <li class="text-gray-600">{{ $ns }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-span-2 mt-8">
                            <h3 class="text-lg font-semibold mb-4">Contact Information</h3>
                            <div class="grid grid-cols-3 gap-6">
                                <!-- Registrant Contact -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium mb-2">Registrant Contact</h4>
                                    @if($domain->registrantContact)
                                        <dl class="space-y-2">
                                            <dt class="text-sm text-gray-500">Name</dt>
                                            <dd>{{ $domain->registrantContact->name }}</dd>
                                            <dt class="text-sm text-gray-500">Organization</dt>
                                            <dd>{{ $domain->registrantContact->organization }}</dd>
                                            <dt class="text-sm text-gray-500">Email</dt>
                                            <dd>{{ $domain->registrantContact->email }}</dd>
                                        </dl>
                                    @else
                                        <p class="text-gray-500">No registrant contact information available.</p>
                                    @endif
                                </div>

                                <!-- Admin Contact -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium mb-2">Administrative Contact</h4>
                                    @if($domain->adminContact)
                                        <dl class="space-y-2">
                                            <dt class="text-sm text-gray-500">Name</dt>
                                            <dd>{{ $domain->adminContact->name }}</dd>
                                            <dt class="text-sm text-gray-500">Organization</dt>
                                            <dd>{{ $domain->adminContact->organization }}</dd>
                                            <dt class="text-sm text-gray-500">Email</dt>
                                            <dd>{{ $domain->adminContact->email }}</dd>
                                        </dl>
                                    @else
                                        <p class="text-gray-500">No administrative contact information available.</p>
                                    @endif
                                </div>

                                <!-- Technical Contact -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium mb-2">Technical Contact</h4>
                                    @if($domain->techContact)
                                        <dl class="space-y-2">
                                            <dt class="text-sm text-gray-500">Name</dt>
                                            <dd>{{ $domain->techContact->name }}</dd>
                                            <dt class="text-sm text-gray-500">Organization</dt>
                                            <dd>{{ $domain->techContact->organization }}</dd>
                                            <dt class="text-sm text-gray-500">Email</dt>
                                            <dd>{{ $domain->techContact->email }}</dd>
                                        </dl>
                                    @else
                                        <p class="text-gray-500">No technical contact information available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
