<?php

// tambahan sutan
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Forgot Password</title>
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
                <h1 class="text-2xl sm:text-3xl font-bold">Password Recovery</h1>
                <p class="mt-2 text-sm sm:text-base opacity-90">We'll send you a link to reset your password.</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="lg:w-1/2 w-full flex items-center justify-center p-6 sm:p-8 lg:p-12">
            <div class="w-full max-w-md form-card rounded-xl shadow-lg p-6 sm:p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Forgot Password</h2>
                    <p class="mt-2 text-sm text-gray-600">Enter your email address and we'll send you a link to reset your password.</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-700 text-sm">{{ session('status') }}</p>
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

                <!-- Forgot Password Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Field -->
                    <div class="input-field relative">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="your@email.com">
                    </div>

                    <!-- Send Reset Link Button -->
                    <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-white font-medium text-lg transition-all duration-300">
                        Send Password Reset Link
                    </button>

                    <!-- Back to Login -->
                    <div class="text-center text-sm text-gray-600 mt-4">
                        <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 font-medium">Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>