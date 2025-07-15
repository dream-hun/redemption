<x-admin-layout>
    <div class="container">
        <h2>Changing Domain Owner</h2>
        <h3>Send Transfer Invitation for {{ $domain->name }}</h3>
        <small>With a change of ownership, you fully relinquish ownership of the domain (along with some associated
            services) and designate a new owner. If the new owner doesn't have a {{ env('APP_NAME') }} account yet, they
            will have the option to create a new one.</small>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('domains.transfer.send', $domain) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="mb-3">
                <label for="recipient_email" class="form-label">New Owner's Email</label>
                <input type="email" name="recipient_email" id="recipient_email"
                       class="form-control @error('recipient_email') is-invalid @enderror" required>
                @error('recipient_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Send Transfer Invitation</button>
            <a href="{{ route('domains.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</x-admin-layout>
