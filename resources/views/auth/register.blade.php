@extends('auth.layouts.base')

@section('title', 'Sign Up')

@section('hero-title', 'Join Jamur Care')
@section('hero-subtitle', 'Start your journey with nature\'s most fascinating creations.')

@section('form-title', 'Create Account')

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

    <!-- Registration Form -->
    <form class="space-y-6"
          method="POST"
          action="{{ route('register') }}">
        @csrf

        <!-- Name Field -->
        <div>
            <label class="text-on-surface mb-1 block text-sm font-medium"
                   for="name">Full Name</label>
            <input class="bg-surface dark:bg-surface-container border-outline focus:ring-primary w-full rounded-lg border px-4 py-3 outline-0 transition-all duration-200 focus:border-transparent focus:ring-2"
                   id="name"
                   name="name"
                   type="text"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   placeholder="John Doe">
        </div>

        <!-- Email Field -->
        <div>
            <label class="text-on-surface mb-1 block text-sm font-medium"
                   for="email">Email</label>
            <input class="bg-surface dark:bg-surface-container border-outline focus:ring-primary w-full rounded-lg border px-4 py-3 outline-0 transition-all duration-200 focus:border-transparent focus:ring-2"
                   id="email"
                   name="email"
                   type="email"
                   value="{{ old('email') }}"
                   required
                   placeholder="your@email.com">
        </div>

        <!-- Password Field -->
        <div>
            <label class="text-on-surface mb-1 block text-sm font-medium"
                   for="password">Password</label>
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
                   for="password_confirmation">Confirm
                Password</label>
            <div class="relative">
                <input class="bg-surface dark:bg-surface-container border-outline focus:ring-primary w-full rounded-lg border px-4 py-3 pr-12 outline-0 transition-all duration-200 focus:border-transparent focus:ring-2"
                       id="password_confirmation"
                       name="password_confirmation"
                       type="password"
                       required
                       placeholder="••••••••">
                <button class="password-toggle absolute right-3 top-1/2 flex -translate-y-1/2 items-center"
                        type="button"
                        onclick="togglePassword('password_confirmation')">
                    <i class="material-symbols-outlined text-on-surface/60 hover:text-on-surface transition-colors"
                       id="eye-icon-password_confirmation">visibility_off</i>
                </button>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start gap-3">
            <input class="text-primary bg-surface dark:bg-surface-container border-outline focus:ring-primary mt-1 h-4 w-4 flex-shrink-0 rounded focus:ring-2"
                   id="terms"
                   name="terms"
                   type="checkbox"
                   required>
            <label class="text-on-surface text-sm"
                   for="terms">
                I agree to the
                <a class="text-primary hover:text-primary/80 font-medium transition-colors"
                   href="#">Terms of
                    Service</a>
                and
                <a class="text-primary hover:text-primary/80 font-medium transition-colors"
                   href="#">Privacy
                    Policy</a>
            </label>
        </div>

        <!-- Register Button -->
        <button class="btn-primary text-on-primary w-full rounded-lg px-4 py-3 text-lg font-medium"
                type="submit">
            Create Account
        </button>

        <!-- Already have account -->
        <div class="text-on-surface/60 text-center text-sm">
            Already have an account?
            <a class="text-primary hover:text-primary/80 font-medium transition-colors"
               href="{{ route('login') }}">Sign
                In</a>
        </div>

        <!-- Divider -->
        <div class="my-6 flex items-center">
            <div class="border-outline flex-1 border-t"></div>
            <span class="text-on-surface/60 px-4 text-sm">Or</span>
            <div class="border-outline flex-1 border-t"></div>
        </div>

        <!-- Social Login Buttons -->
        <div class="flex flex-col items-center justify-center gap-2">
            @foreach (['google', 'github'] as $provider)
                <a class="btn-primary text-on-primary flex w-full w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-lg font-medium"
                   href="{{ route('social.login', ['provider' => $provider]) }}">
                    <img class="h-5 w-5"
                         src="{{ asset('img/' . $provider . '.png') }}"
                         alt="{{ Str::ucwords($provider) }}">
                    <span>Sign in with {{ Str::ucwords($provider) }}</span>
                </a>
            @endforeach
        </div>
    </form>
@endsection
