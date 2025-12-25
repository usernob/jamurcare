@extends('auth.layouts.base')

@section('title', 'Reset Password')

@section('hero-title', 'Reset Your Password')
@section('hero-subtitle', 'Choose a new password that you\'ll remember.')

@section('form-title', 'Reset Password')

@section('content')
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

    <!-- Reset Password Form -->
    <form class="space-y-6"
          method="POST"
          action="{{ route('password.store') }}">
        @csrf

        <!-- Hidden Token -->
        <input name="token"
               type="hidden"
               value="{{ $request->route('token') }}">

        <!-- Email Field -->
        <div>
            <label class="text-on-surface mb-1 block text-sm font-medium"
                   for="email">Email Address</label>
            <input class="dark:bg-surface-container border-outline focus:ring-primary w-full rounded-lg border bg-white px-4 py-3 transition-all duration-200 focus:border-transparent focus:ring-2"
                   id="email"
                   name="email"
                   type="email"
                   value="{{ old('email', $request->email) }}"
                   required
                   autofocus
                   placeholder="your@email.com">
        </div>

        <!-- Password Field -->
        <div>
            <label class="text-on-surface mb-1 block text-sm font-medium"
                   for="password">New Password</label>
            <div class="relative">
                <input class="bg-surface dark:bg-surface-container border-outline focus:ring-primary w-full rounded-lg border px-4 py-3 pr-12 outline-0 transition-all duration-200 focus:border-transparent focus:ring-2"
                       id="password"
                       name="password"
                       type="password"
                       required
                       placeholder="••••••••">
                <button class="password-toggle absolute right-3 top-1/2 flex -translate-y-1/2 items-center"
                        type="button"
                        onclick="togglePassword('password')">
                    <i class="material-symbols-outlined text-on-surface/60 hover:text-on-surface transition-colors"
                       id="eye-icon-password">visibility_off</i>
                </button>
            </div>
            <div class="password-strength mt-2"
                 id="password-strength"></div>
            <p class="text-on-surface/60 mt-1 text-xs"
               id="password-help">
                Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.
            </p>
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label class="text-on-surface mb-1 block text-sm font-medium"
                   for="password_confirmation">Confirm New
                Password</label>
            <div class="relative">
                <input class="dark:bg-surface-container border-outline focus:ring-primary w-full rounded-lg border bg-white px-4 py-3 pr-12 transition-all duration-200 focus:border-transparent focus:ring-2"
                       id="password_confirmation"
                       name="password_confirmation"
                       type="password"
                       required
                       placeholder="••••••••">
                <button class="password-toggle absolute right-3 top-1/2 -translate-y-1/2"
                        type="button"
                        onclick="togglePassword('password_confirmation')">
                    <i class="fa fa-eye-slash text-on-surface/60 hover:text-on-surface transition-colors"
                       id="eye-icon-password_confirmation"></i>
                </button>
            </div>
        </div>

        <!-- Reset Password Button -->
        <button class="btn-primary text-on-primary w-full rounded-lg px-4 py-3 text-lg font-medium"
                type="submit">
            Reset Password
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
