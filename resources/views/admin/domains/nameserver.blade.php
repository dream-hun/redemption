
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Nameservers</h3>
        </div>
        <div class="card-body">
            <p class="text-muted">Enter at least 2 nameservers. You can add up to 13 nameservers.</p>
            <form action="{{ route('admin.domains.nameservers.update', $domain->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                <div id="nameservers-container">
                    @php
                        // Ensure nameservers are loaded
                        $domain->load('nameservers');
                        // Get nameservers as array from the domain model
                        $nameservers = $domain->nameservers ?? [];
                        // If nameservers is empty, create default entries
                        if (empty($nameservers) || (is_countable($nameservers) && count($nameservers) === 0)) {
                            $nameservers = [
                                ['hostname' => ''],
                                ['hostname' => '']
                            ];
                        }
                    @endphp

                    @foreach($nameservers as $index => $nameserver)
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nameserver {{ $index + 1 }}</span>
                            </div>
                            <input type="text" class="form-control" name="nameservers[]" 
                                value="{{ old('nameservers.'.$index, 
                                    is_object($nameserver) && isset($nameserver->hostname) ? $nameserver->hostname : 
                                    (is_array($nameserver) && isset($nameserver['hostname']) ? $nameserver['hostname'] : 
                                    (is_string($nameserver) ? $nameserver : ''))) }}" 
                                placeholder="ns{{ $index + 1 }}.example.com">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger remove-nameserver" {{ count($nameservers) <= 2 ? 'disabled' : '' }}>
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                    @endforeach

                </div>

                @error('nameservers')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                @error('nameservers.*')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <button type="button" class="btn btn-secondary mt-2" id="add-nameserver">
                    <i class="fas fa-plus"></i> Add Nameserver
                </button>
                <div class="mt-3">
                    <a href="{{ route('admin.domains.show', $domain->uuid) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Nameservers
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('nameservers-container');
            const addButton = document.getElementById('add-nameserver');

            // Add nameserver event
            addButton.addEventListener('click', function() {
                const count = container.children.length;

                if (count >= 13) {
                    alert('Maximum 13 nameservers allowed');
                    return;
                }

                const div = document.createElement('div');
                div.className = 'input-group mb-3';
                div.innerHTML = `
                    <div class="input-group-prepend">
                        <span class="input-group-text">Nameserver ${count + 1}</span>
                    </div>
                    <input type="text" name="nameservers[]" class="form-control" placeholder="ns${count + 1}.example.com">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-nameserver">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                `;
                container.appendChild(div);

                // Add event listener to the new remove button
                const newRemoveButton = div.querySelector('.remove-nameserver');
                newRemoveButton.addEventListener('click', removeNameserverHandler);

                // Update button states
                updateRemoveButtons();
            });

            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-nameserver').forEach(button => {
                button.addEventListener('click', removeNameserverHandler);
            });

            // Remove nameserver handler
            function removeNameserverHandler() {
                if (container.children.length > 2) {
                    this.closest('.input-group').remove();
                    updateNameserverNumbers();
                    updateRemoveButtons();
                } else {
                    alert('Minimum 2 nameservers required');
                }
            }

            // Update nameserver numbers
            function updateNameserverNumbers() {
                const nameservers = container.querySelectorAll('.input-group');
                nameservers.forEach((nameserver, index) => {
                    const label = nameserver.querySelector('.input-group-text');
                    if (label) {
                        label.textContent = `Nameserver ${index + 1}`;
                    }
                });
            }

            // Update remove buttons state
            function updateRemoveButtons() {
                const removeButtons = container.querySelectorAll('.remove-nameserver');
                const disabled = container.children.length <= 2;

                removeButtons.forEach(button => {
                    button.disabled = disabled;
                });
            }

            // Initialize
            updateRemoveButtons();
        });
    </script>
