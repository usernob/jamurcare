@extends('auth.layouts.base')

@section('title', 'Sign Up')

@section('hero-title', 'Join Jamur Care')
@section('hero-subtitle', 'Start your journey with nature\'s most fascinating creations.')

@section('form-title', 'Create Account')

@section('content')
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

    <!-- Registration Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-on-surface mb-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full px-4 py-3 bg-surface dark:bg-surface-container outline-0 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                placeholder="John Doe">
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-on-surface mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
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
            <div class="password-strength mt-2" id="password-strength"></div>
            <p class="text-xs text-on-surface/60 mt-1" id="password-help">
                Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.
            </p>
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-on-surface mb-1">Confirm
                Password</label>
            <div class="relative">
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 pr-12 bg-surface dark:bg-surface-container outline-0 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                    placeholder="••••••••">
                <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 flex items-center"
                    onclick="togglePassword('password_confirmation')">
                    <i class="material-symbols-outlined text-on-surface/60 hover:text-on-surface transition-colors"
                        id="eye-icon-password_confirmation">visibility_off</i>
                </button>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start gap-3">
            <input id="terms" type="checkbox" name="terms" required
                class="mt-1 h-4 w-4 text-primary bg-surface dark:bg-surface-container border-outline rounded focus:ring-2 focus:ring-primary flex-shrink-0">
            <label for="terms" class="text-sm text-on-surface">
                I agree to the
                <a href="#" class="text-primary hover:text-primary/80 font-medium transition-colors">Terms of
                    Service</a>
                and
                <a href="#" class="text-primary hover:text-primary/80 font-medium transition-colors">Privacy
                    Policy</a>
            </label>
        </div>

        <!-- Register Button -->
        <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-on-primary font-medium text-lg">
            Create Account
        </button>

        <!-- Already have account -->
        <div class="text-center text-sm text-on-surface/60">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 font-medium transition-colors">Sign
                In</a>
        </div>

        <!-- Divider -->
        <div class="flex items-center my-6">
            <div class="flex-1 border-t border-outline"></div>
            <span class="px-4 text-sm text-on-surface/60">Or</span>
            <div class="flex-1 border-t border-outline"></div>
        </div>

        <!-- Social Login Buttons -->
        <div class="flex flex-col gap-2 items-center justify-center">
            @foreach (['google', 'github'] as $provider)
                <a href="{{ route('social.login', ['provider' => $provider]) }}"
                    class="w-full flex items-center justify-center btn-primary w-full py-3 px-4 rounded-lg text-on-primary font-medium text-lg gap-2">
                    <img src="{{ asset('img/' . $provider . '.png') }}" alt="{{ Str::ucwords($provider) }}" class="w-5 h-5">
                    <span>Sign in with {{ Str::ucwords($provider) }}</span>
                </a>
            @endforeach
        </div>
    </form>
@endsection
