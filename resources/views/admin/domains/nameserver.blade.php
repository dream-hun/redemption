
    <div class="card">
        <div class="card-header bg-light text-white">
            <h3 class="card-title">Update Name servers for {{$domain->name}}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted">Enter at least 2 nameservers. You can add up to 13 nameservers.</p>
            <form action="{{ route('admin.domains.nameservers.update', $domain->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                <div id="nameservers-container">
                    <div class="row">
                        <div class="col-md-6">
                            @foreach($domain->nameservers ?? [] as $index => $nameserver)

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Nameserver {{ $index + 1 }}</span>
                                    </div>
                                    <input type="text" name="nameservers[]" class="form-control" value="{{ old('nameservers.'.$index, $nameserver) }}" placeholder="ns1.example.com">
                                    @if($index > 1)
                                        <button type="button" class="btn btn-danger" onclick="removeNameserver(this)">X</button>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                        </div>

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
                <span class="input-group-text">Nameserver ${count + 1}</span>
                <input type="text" name="nameservers[]" class="form-control" placeholder="ns1.example.com">
                <button type="button" class="btn btn-danger" onclick="removeNameserver(this)">X</button>
            `;
            container.appendChild(div);
        }

        function removeNameserver(button) {
            const container = document.getElementById('nameservers-container');
            if (container.children.length > 2) {
                button.closest('.input-group').remove();
            } else {
                alert('Minimum 2 nameservers required');
            }
        }
    </script>
