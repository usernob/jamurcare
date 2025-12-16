<?php

// tambahan sutan
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Sign Up</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-mushroom {
            background-image: url("{{asset('img/backround-login.png')}}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .bg-leaf-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M25 25h50v50H25z' fill='%23f5f5f5' opacity='0.1'/%3E%3C/svg%3E");
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }

        .password-toggle:hover {
            color: #1f2937;
        }

        .social-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-google {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .social-facebook {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .social-microsoft {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .form-card {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }

        .input-field:focus-within {
            box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.2);
            border-color: #6b7280;
        }

        .btn-primary {
            background: linear-gradient(135deg, #86af7b, #6b8e73);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #7a9d6f, #5d7b64);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.2);
        }

        .btn-social {
            transition: all 0.2s ease;
        }

        .btn-social:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 4px;
            transition: all 0.3s ease;
        }

        .strength-weak {
            background-color: #ef4444;
        }

        .strength-medium {
            background-color: #f59e0b;
        }

        .strength-strong {
            background-color: #10b981;
        }

        .strength-very-strong {
            background-color: #16a34a;
        }
    </style>
</head>
<body class="font-sans antialiased bg-mushroom">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Image -->
        <div class="lg:w-1/2 w-full h-[30vh] lg:h-screen relative overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute top-0 left-6 z-10">
                <img src="{{asset('img/logo-full.png')}}" alt="Jamur Care Logo Putih" class="h-40">
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <h1 class="text-2xl sm:text-3xl font-bold">Join Jamur Care</h1>
                <p class="mt-2 text-sm sm:text-base opacity-90">Start your journey with nature's most fascinating creations.</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="lg:w-1/2 w-full flex items-center justify-center p-6 sm:p-8 lg:p-12">
            <div class="w-full max-w-md form-card rounded-xl shadow-lg p-6 sm:p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Create Account</h2>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="list-disc list-inside text-red-700 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name Field -->
                    <div class="input-field relative">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="John Doe">
                    </div>

                    <!-- Email Field -->
                    <div class="input-field relative">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="your@email.com">
                    </div>

                    <!-- Password Field -->
                    <div class="input-field relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 pr-10"
                               placeholder="••••••••">
                        <span class="password-toggle" onclick="togglePassword('password')">
                            <i class="fa fa-eye-slash" id="eye-icon-password"></i>
                        </span>
                        <div class="password-strength mt-2" id="password-strength"></div>
                        <p class="text-xs text-gray-500 mt-1" id="password-help">Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.</p>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="input-field relative">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 pr-10"
                               placeholder="••••••••">
                        <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fa fa-eye-slash" id="eye-icon-password_confirmation"></i>
                        </span>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5 w-5">
                            <input id="terms" type="checkbox" name="terms" required
                                   class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">
                                I agree to the <a href="#" class="text-green-600 hover:text-green-800">Terms of Service</a> and <a href="#" class="text-green-600 hover:text-green-800">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <!-- Register Button -->
                    <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-white font-medium text-lg transition-all duration-300">
                        Create Account
                    </button>

                    <!-- Already have account -->
                    <div class="text-center text-sm text-gray-600 mt-4">
                        Already have an account? <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 font-medium">Sign In</a>
                    </div>

                    <!-- Divider -->
                    <div class="flex items-center my-6">
                        <div class="flex-1 border-t border-gray-300"></div>
                        <span class="px-4 text-sm text-gray-500">Or</span>
                        <div class="flex-1 border-t border-gray-300"></div>
                    </div>

                    <!-- Social Login Buttons -->
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Google Login -->
                        <a href="{{ route('social.login', ['provider' => 'google']) }}"
                           class="btn-social flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="social-icon social-google">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12.545 10.239v3.821h5.445c-.712 2.315-2.647 3.972-5.445 3.972a6.38 6.38 0 01-6.38-6.38 6.38 6.38 0 016.38-6.38c1.875 0 3.667.682 5.018 1.806l3.218-3.218A11.29 11.29 0 0012.545 0C5.623 0 0 5.623 0 12.545 0 19.468 5.623 25.09 12.545 25.09c6.923 0 12.545-5.622 12.545-12.545 0-6.922-5.622-12.545-12.545-12.545z"/>
                                </svg>
                            </div>
                        </a>

                        <!-- Facebook Login -->
                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                           class="btn-social flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="social-icon social-facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.991 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                        </a>

                        <!-- Microsoft Login -->
                        <a href="{{ route('social.login', ['provider' => 'microsoft']) }}"
                           class="btn-social flex items-center justify-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="social-icon social-microsoft">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2 2h8v8H2zm10 0h8v8h-8zM2 12h8v8H2zm10 0h8v8h-8z"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eye-icon-' + fieldId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }

        // Password strength indicator
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordStrength = document.getElementById('password-strength');
            const passwordHelp = document.getElementById('password-help');

            if (passwordInput && passwordStrength) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    // Check length
                    if (password.length >= 8) strength += 1;
                    // Check for uppercase
                    if (/[A-Z]/.test(password)) strength += 1;
                    // Check for lowercase
                    if (/[a-z]/.test(password)) strength += 1;
                    // Check for numbers
                    if (/\d/.test(password)) strength += 1;
                    // Check for special characters
                    if (/[^A-Za-z0-9]/.test(password)) strength += 1;

                    // Update strength indicator
                    passwordStrength.className = 'password-strength';
                    switch(strength) {
                        case 0:
                        case 1:
                            passwordStrength.classList.add('strength-weak');
                            passwordHelp.textContent = 'Weak password. Use at least 8 characters including uppercase, lowercase, number, and special character.';
                            break;
                        case 2:
                            passwordStrength.classList.add('strength-medium');
                            passwordHelp.textContent = 'Medium password. Add more variety to strengthen it.';
                            break;
                        case 3:
                            passwordStrength.classList.add('strength-medium');
                            passwordHelp.textContent = 'Good password. Consider adding a special character for maximum security.';
                            break;
                        case 4:
                            passwordStrength.classList.add('strength-strong');
                            passwordHelp.textContent = 'Strong password. You\'re doing great!';
                            break;
                        case 5:
                            passwordStrength.classList.add('strength-very-strong');
                            passwordHelp.textContent = 'Very strong password. Excellent security!';
                            break;
                    }
                });
            }
        });

        // Auto-focus first input on mobile
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 768) {
                const firstInput = document.querySelector('input[type="text"]');
                if (firstInput) {
                    firstInput.focus();
                }
            }
        });
    </script>
</body>
</html>
