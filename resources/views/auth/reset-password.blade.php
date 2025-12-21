@extends('auth.layouts.base')

@section('title', 'Reset Password')

@section('hero-title', 'Reset Your Password')
@section('hero-subtitle', 'Choose a new password that you\'ll remember.')

@section('form-title', 'Reset Password')

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

    <!-- Reset Password Form -->
    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <!-- Hidden Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-on-surface mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                autofocus
                class="w-full px-4 py-3 bg-white dark:bg-surface-container border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                placeholder="your@email.com">
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-on-surface mb-1">New Password</label>
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
            <label for="password_confirmation" class="block text-sm font-medium text-on-surface mb-1">Confirm New
                Password</label>
            <div class="relative">
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 pr-12 bg-white dark:bg-surface-container border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                    placeholder="••••••••">
                <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2"
                    onclick="togglePassword('password_confirmation')">
                    <i class="fa fa-eye-slash text-on-surface/60 hover:text-on-surface transition-colors"
                        id="eye-icon-password_confirmation"></i>
                </button>
            </div>
        </div>

        <!-- Reset Password Button -->
        <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-on-primary font-medium text-lg">
            Reset Password
        </button>

        <!-- Back to Login -->
        <div class="text-center text-sm text-on-surface/60">
            <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 font-medium transition-colors">
                Back to Sign In
            </a>
        </div>
    </form>
@endsection
