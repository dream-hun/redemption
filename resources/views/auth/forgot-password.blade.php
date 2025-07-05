<x-guest-layout>
    @section('page-title')
        Forgot Password
    @endsection

    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
        }

        .logo-section {
            text-align: center;
            padding: 40px 40px 20px;
        }

        .logo-section img {
            max-width: 80px;
            height: auto;
        }

        .form-section {
            padding: 20px 40px 40px;
        }

        .form-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .signin-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .signin-link a {
            color: #007bff;
            text-decoration: none;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }

        .error-text {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .status-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>

    <div class="register-container">
        <div class="register-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('logo.webp') }}" alt="{{ config('app.name') }} Logo">
                </a>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                <p style="text-align: center; margin-bottom: 20px;">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
                </p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="status-message">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                    @csrf
                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                    <h2 class="form-title">Reset Password</h2>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input id="email"
                               type="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               required
                               autofocus>
                        @error('email')
                        <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-primary">Send Password Reset Link</button>
                    </div>

                    <div class="signin-link">
                        Remember your password?
                        <a href="{{ route('login') }}">Back to Login</a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
            </div>
        </div>
    </div>
        @push('scripts')
            <script>
                grecaptcha.ready(function () {
                    document.getElementById('forgotForm').addEventListener("submit", function (event) {
                        event.preventDefault();
                        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'forgot'})
                            .then(function (token) {
                                document.getElementById("recaptcha_token").value = token;
                                document.getElementById('forgotForm').submit();
                            });
                    });
                });
            </script>
        @endpush
</x-guest-layout>
