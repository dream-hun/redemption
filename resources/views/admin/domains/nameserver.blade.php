
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
                    @foreach($domain->nameservers ?? [] as $index => $nameserver)
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nameserver {{ $index + 1 }}</span>
                            </div>
                            <input type="text" class="form-control" name="nameservers[]" value="{{old('nameservers.'.$index,$nameserver)}}" placeholder="ns1.example.com">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger" onclick="removeNameserver(this)" {{ count($domain->nameservers ?? []) <= 2 ? 'disabled' : '' }}>
                                    <i class="bi bi-x"></i> Remove
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

                <button type="button" class="btn btn-secondary mt-2" onclick="addNameserver()">Add Nameserver</button>
                <div class="mt-3">
                    <a href="{{ route('admin.domains.show', $domain->uuid) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Nameservers</button>
                </div>
            </form>
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
            div.className = 'input-group mb-3';
            div.innerHTML = `
                <div class="input-group-prepend">
                    <span class="input-group-text">Nameserver ${count + 1}</span>
                </div>
                <input type="text" name="nameservers[]" class="form-control" placeholder="ns1.example.com">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" onclick="removeNameserver(this)">
                        <i class="bi bi-x"></i> Remove
                    </button>
                </div>
            `;
            container.appendChild(div);
            
            // Enable all remove buttons when we have more than 2 nameservers
            updateRemoveButtons();
        }

        function removeNameserver(button) {
            const container = document.getElementById('nameservers-container');
            if (container.children.length > 2) {
                button.closest('.input-group').remove();
                // Update the numbering of the nameservers
                updateNameserverNumbers();
                // Disable remove buttons if we're down to 2 nameservers
                updateRemoveButtons();
            } else {
                alert('Minimum 2 nameservers required');
            }
        }
        
        function updateNameserverNumbers() {
            const container = document.getElementById('nameservers-container');
            const nameservers = container.querySelectorAll('.input-group');
            nameservers.forEach((nameserver, index) => {
                const label = nameserver.querySelector('.input-group-text');
                if (label) {
                    label.textContent = `Nameserver ${index + 1}`;
                }
            });
        }
        
        function updateRemoveButtons() {
            const container = document.getElementById('nameservers-container');
            const removeButtons = container.querySelectorAll('.btn-danger');
            const disabled = container.children.length <= 2;
            
            removeButtons.forEach(button => {
                button.disabled = disabled;
            });
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateRemoveButtons();
        });
    </script>
