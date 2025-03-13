@extends('layouts.user')

@section('content')
                  <livewire:domain-search/>


@endsection

@push('scripts')
<script>
    // Listen for Livewire notifications
    window.addEventListener('notify', event => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: event.detail.type.charAt(0).toUpperCase() + event.detail.type.slice(1),
                text: event.detail.message,
                icon: event.detail.type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback to alert if SweetAlert2 is not available
            alert(event.detail.message);
        }
    });
</script>
@endpush

@section('styles')
<style>
    .domain-search-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .domain-search-loading {
        position: relative;
        opacity: 0.7;
        pointer-events: none;
    }

    .domain-search-loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.5);
    }

    .pricing-btn.in-cart {
        background-color: #28a745;
        cursor: not-allowed;
    }

    [wire\:loading] {
        opacity: 0.7;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
</style>
@endsection
