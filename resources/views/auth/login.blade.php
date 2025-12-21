@extends('auth.layouts.base')

@section('title', 'Sign In')

@section('hero-title', 'Welcome to Jamur Care')
@section('hero-subtitle', 'Discover the beauty of nature\'s wonders with us.')

@section('form-title', 'Sign In')

@section('content')
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-green-700 dark:text-green-400 text-sm">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <ul class="list-disc list-inside text-red-700 dark:text-red-400 text-sm space-y-1">
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
        <div>
            <label for="email" class="block text-sm font-medium text-on-surface mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-3 bg-surface dark:bg-surface-container outline-0 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                placeholder="your@email.com">
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-on-surface mb-1">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-3 pr-12 bg-surface dark:bg-surface-container outline-0 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                    placeholder="••••••••">
                <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 flex items-center"
                    onclick="togglePassword('password')">
                    <i class="material-symbols-outlined text-on-surface/60 hover:text-on-surface transition-colors"
                        id="eye-icon-password">visibility_off</i>
                </button>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="h-4 w-4 text-primary bg-surface dark:bg-surface-container border-outline rounded focus:ring-2 focus:ring-primary">
                <label for="remember_me" class="ml-2 block text-sm text-on-surface">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}"
                class="text-sm text-primary hover:text-primary/80 font-medium transition-colors">
                Forgot password?
            </a>
        </div>

        <!-- Sign In Button -->
        <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-on-primary font-medium text-lg">
            Sign In
        </button>

        <!-- New User -->
        <div class="text-center text-sm text-on-surface/60">
            New User? <a href="{{ route('register') }}"
                class="text-primary hover:text-primary/80 font-medium transition-colors">Sign Up</a>
        </div>

        <!-- Divider -->
        <div class="flex items-center my-6">
            <div class="flex-1 border-t border-outline"></div>
            <span class="px-4 text-sm text-on-surface/60">Or</span>
            <div class="flex-1 border-t border-outline"></div>
        </div>

        <!-- Social Login Buttons -->
        <div class="grid grid-cols-3 gap-3">
            <!-- Google Login -->
            <a href="{{ route('social.login', ['provider' => 'google']) }}"
                class="flex items-center justify-center p-3 bg-surface-container border border-outline rounded-lg hover:bg-primary-variant transition-all duration-200">
                <img src="{{ asset('img/google.png') }}" alt="Google" class="w-5 h-5">
            </a>

            <!-- Facebook Login -->
            <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                class="flex items-center justify-center p-3 bg-surface-container border border-outline rounded-lg hover:bg-primary-variant transition-all duration-200">
                <img src="{{ asset('img/facebook.png') }}" alt="Facebook" class="w-5 h-5">
            </a>

            <!-- Microsoft Login -->
            <a href="{{ route('social.login', ['provider' => 'microsoft']) }}"
                class="flex items-center justify-center p-3 bg-surface-container border border-outline rounded-lg hover:bg-primary-variant transition-all duration-200">
                <img src="{{ asset('img/icons/microsoft.png') }}" alt="Microsoft" class="w-5 h-5">
            </a>
        </div>
    </form>
@endsection
