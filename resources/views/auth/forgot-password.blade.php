@extends('auth.layouts.base')

@section('title', 'Forgot Password')

@section('hero-title', 'Password Recovery')
@section('hero-subtitle', 'We\'ll send you a link to reset your password.')

@section('form-title', 'Forgot Password')
@section('form-subtitle', 'Enter your email address and we\'ll send you a link to reset your password.')

@section('content')
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <ul class="list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-400">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Forgot Password Form -->
    <form class="space-y-6"
          method="POST"
          action="{{ route('password.email') }}">
        @csrf

        <!-- Email Field -->
        <div>
            <label class="text-on-surface mb-1 block text-sm font-medium"
                   for="email">Email Address</label>
            <input class="bg-surface dark:bg-surface-container border-outline focus:ring-primary w-full rounded-lg border px-4 py-3 outline-0 transition-all duration-200 focus:border-transparent focus:ring-2"
                   id="email"
                   name="email"
                   type="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   placeholder="your@email.com">
        </div>

        <!-- Send Reset Link Button -->
        <button class="btn-primary text-on-primary w-full rounded-lg px-4 py-3 text-lg font-medium"
                type="submit">
            Send Password Reset Link
        </button>

        <!-- Back to Login -->
        <div class="text-on-surface/60 text-center text-sm">
            <a class="text-primary hover:text-primary/80 font-medium transition-colors"
               href="{{ route('login') }}">
                Back to Sign In
            </a>
        </div>
    </form>
@endsection
