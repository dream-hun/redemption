@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (event) => {
            const type = event[0].type;
            const message = event[0].message;
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            notification.setAttribute('role', 'alert');
            notification.style.zIndex = '9999';
            
            // Add message
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Add to document
            document.body.appendChild(notification);
            
            // Auto dismiss after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        });
    });
</script>
@endpush

<div></div>
