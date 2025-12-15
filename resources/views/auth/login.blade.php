<?php

// Tambahan Sutan
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Sign In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-mushroom {
            background-image: url('https://images.unsplash.com/photo-1597236049766-5e6b4a9d679c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
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
    </style>
</head>
<body class="font-sans antialiased bg-cream">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Image -->
        <div class="lg:w-1/2 w-full h-[30vh] lg:h-screen relative overflow-hidden bg-mushroom">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute top-6 left-6 z-10">
                <img src="https://via.placeholder.com/120x40?text=Jamur+Care" alt="Jamur Care Logo" class="h-10 sm:h-12">
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <h1 class="text-2xl sm:text-3xl font-bold">Welcome to Jamur Care</h1>
                <p class="mt-2 text-sm sm:text-base opacity-90">Discover the beauty of nature's wonders with us.</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="lg:w-1/2 w-full flex items-center justify-center p-6 sm:p-8 lg:p-12">
            <div class="w-full max-w-md form-card rounded-xl shadow-lg p-6 sm:p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Sign In</h2>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

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

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Field -->
                    <div class="input-field relative">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
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
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-white font-medium text-lg transition-all duration-300">
                        Sign In
                    </button>

                    <!-- New User -->
                    <div class="text-center text-sm text-gray-600 mt-4">
                        New User? <a href="{{ route('register') }}" class="text-green-600 hover:text-green-800 font-medium">Sign Up</a>
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

        // Auto-focus first input on mobile
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 768) {
                const firstInput = document.querySelector('input[type="email"]');
                if (firstInput) {
                    firstInput.focus();
                }
            }
        });
    </script>
</body>
</html>