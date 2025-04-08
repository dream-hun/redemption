<x-guest-layout>
    <div class="rts-sign-up-section">
        <div class="section-inner">
            <div class="logo-area">
                <a href="{{ route('home') }}"><img src="{{ asset('logo.webp') }}" alt="{{ config('app.name') }} Logo"></a>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <h2 class="form-title">Create Account</h2>
                <div class="form-inner">
                    <div class="single-wrapper">
                        <input type="text"
                               placeholder="Full name"
                               name="name"
                               value="{{ old('name') }}"
                               class="@error('name') is-invalid @enderror"
                               required
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="single-wrapper">
                        <input type="email"
                               placeholder="Email address"
                               name="email"
                               value="{{ old('email') }}"
                               class="@error('email') is-invalid @enderror"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="single-wrapper">
                        <input type="password"
                               name="password"
                               placeholder="Password"
                               class="@error('password') is-invalid @enderror"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="single-wrapper">
                        <input type="password"
                               name="password_confirmation"
                               placeholder="Confirm Password"
                               required>
                    </div>

                    {{--<div class="check">
                        <div class="check-box-area">
                            <input type="checkbox"
                                   id="terms"
                                   name="terms"
                                   class="@error('terms') is-invalid @enderror"
                                   required>
                            <label for="terms">I agree to the <a href="{{ route('terms') }}" target="_blank">Terms of Service</a></label>
                            @error('terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>--}}

                    <div class="form-btn">
                        <button type="submit" class="primary__btn">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </div>
                </div>
                <p class="sign-in-option">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
            </form>
        </div>
        <div class="copyright-area">
            <p>&copy;{{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
        </div>
    </div>
</x-guest-layout>



