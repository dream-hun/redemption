<x-guest-layout>
    <div class="container py-4">
        <div class="alert alert-info small" role="alert">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success small" role="alert">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="d-flex justify-content-between mt-4">
            <!-- Resend Verification Email -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    {{ __('Resend Verification Email') }}
                </button>
            </form>

            <!-- Log Out -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

