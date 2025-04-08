<x-guest-layout>
    <div class="rts-sign-up-section">
        <div class="section-inner">
            <div class="logo-area">
                <a href="{{ route('home') }}"><img src="{{ asset('logo.webp') }}" alt="{{ config('app.name') }} Logo"></a>
            </div>
            
            @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h2 class="form-title">Sign In</h2>
                <div class="form-inner">
                    <div class="single-wrapper">
                        <input type="email" 
                               placeholder="Your email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="@error('email') is-invalid @enderror"
                               required 
                               autofocus>
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

                    <div class="check">
                        <div class="check-box-area">
                            <input type="checkbox" id="remember_me" name="remember">
                            <label for="remember_me">Remember me</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                        @endif
                    </div>

                    <div class="form-btn">
                        <button type="submit" class="primary__btn">
                            <i class="fas fa-sign-in-alt"></i> Log In
                        </button>
                    </div>
                </div>
                <p class="sign-in-option">Don't have an account yet? <a href="{{ route('register') }}">Sign Up</a></p>
            </form>
        </div>
        <div class="copyright-area">
            <p>&copy;{{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
        </div>
    </div>
</x-guest-layout>
