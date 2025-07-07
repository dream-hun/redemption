<x-guest-layout>
    @section('page-title')
        Reset Password
    @endsection

    <style>
        body {
            background-color: #f8f9fa;
        }

        .reset-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-card {
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
            margin-bottom: 30px;
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
        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
        }

        .password-toggle:hover {
            color: #333;
        }
    </style>

    <div class="reset-container">
        <div class="reset-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('logo.webp') }}" alt="{{ config('app.name') }} Logo">
                </a>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                @if (session('status'))
                    <div class="status-message">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" id="resetForm">
                    @csrf

                    <!-- Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                    <h2 class="form-title">Reset Password</h2>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input id="email"
                               type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email', $request->email) }}"
                               required
                               autofocus
                               autocomplete="username">
                        @error('email')
                        <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group" x-data="{ showPassword: false }">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-field">
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   class="form-control"
                                   required
                                   autocomplete="new-password">
                            <button type="button"
                                    class="password-toggle"
                                    @click="showPassword = !showPassword"
                                    :title="showPassword ? 'Hide password' : 'Show password'">
                                <span x-show="!showPassword"><i class="bi bi-eye"></i></span>
                                <span x-show="showPassword"><i class="bi bi-eye-slash"></i></span>
                            </button>
                        </div>
                        @error('password')
                        <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group" x-data="{ showConfirmPassword: false }">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="password-field">
                            <input id="password_confirmation"
                                   :type="showConfirmPassword ? 'text' : 'password'"
                                   name="password_confirmation"
                                   class="form-control"
                                   required
                                   autocomplete="new-password">
                            <button type="button"
                                    class="password-toggle"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    :title="showConfirmPassword ? 'Hide password' : 'Show password'">
                                <span x-show="!showConfirmPassword"><i class="bi bi-eye"></i></span>
                                <span x-show="showConfirmPassword"><i class="bi bi-eye-slash"></i></span>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn-primary">
                            Reset Password
                        </button>
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
                    document.getElementById('resetForm').addEventListener("submit", function (event) {
                        event.preventDefault();
                        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'reset'})
                            .then(function (token) {
                                document.getElementById("recaptcha_token").value = token;
                                document.getElementById('resetForm').submit();
                            });
                    });
                });
            </script>
        @endpush
</x-guest-layout>
