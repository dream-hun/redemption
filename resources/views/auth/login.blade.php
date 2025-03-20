<x-guest-layout>
    <div class="rts-sign-up-section">
        <div class="section-inner">
            <div class="logo-area">
                <a href="{{ route('home') }}"><img src="{{ asset('logo.webp') }}" alt=""></a>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h2 class="form-title">Sign In</h2>
                <div class="form-inner">
                    <div class="single-wrapper">
                        <input type="email" placeholder="Your email" name="email" required>
                    </div>
                    <div class="single-wrapper">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="check">
                        <div class="check-box-area">
                            <input type="checkbox" id="remember_me" name="remenber" />
                            <label for="remember_me">Remember me</label>
                        </div>
                        @if (Route::has('password'))
                            <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                        @endif

                    </div>
                    <div class="form-btn">
                        <button type="submit" class="primary__btn">Log In</button>
                    </div>
                </div>
                <p class="sign-in-option">Have no account yet? <a href="{{ route('register') }}">Sign Up</a></p>
            </form>
        </div>
        <div class="copyright-area">
            <p>&copy;{{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
        </div>
    </div>
</x-guest-layout>
