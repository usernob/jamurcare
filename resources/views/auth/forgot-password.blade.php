@extends('auth.layouts.base')

@section('title', 'Forgot Password')

@section('hero-title', 'Password Recovery')
@section('hero-subtitle', 'We\'ll send you a link to reset your password.')

@section('form-title', 'Forgot Password')
@section('form-subtitle', 'Enter your email address and we\'ll send you a link to reset your password.')

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

    <!-- Forgot Password Form -->
    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-on-surface mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-3 bg-surface dark:bg-surface-container outline-0 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                placeholder="your@email.com">
        </div>

        <!-- Send Reset Link Button -->
        <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-on-primary font-medium text-lg">
            Send Password Reset Link
        </button>

        <!-- Back to Login -->
        <div class="text-center text-sm text-on-surface/60">
            <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 font-medium transition-colors">
                Back to Sign In
            </a>
        </div>
    </form>
@endsection
