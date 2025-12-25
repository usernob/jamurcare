@extends('auth.layouts.base')

@section('title', 'Sign In')

@section('hero-title', 'Welcome to Jamur Care')
@section('hero-subtitle', 'Discover the beauty of nature\'s wonders with us.')

@section('form-title', 'Sign In')

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

    <!-- Login Form -->
    <form class="space-y-6"
          method="POST"
          action="{{ route('login') }}">
        @csrf

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
                   autofocus
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
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input class="text-primary bg-surface dark:bg-surface-container border-outline focus:ring-primary h-4 w-4 rounded focus:ring-2"
                       id="remember_me"
                       name="remember"
                       type="checkbox">
                <label class="text-on-surface ml-2 block text-sm"
                       for="remember_me">Remember me</label>
            </div>
            <a class="text-primary hover:text-primary/80 text-sm font-medium transition-colors"
               href="{{ route('password.request') }}">
                Forgot password?
            </a>
        </div>

        <!-- Sign In Button -->
        <button class="btn-primary text-on-primary w-full rounded-lg px-4 py-3 text-lg font-medium"
                type="submit">
            Sign In
        </button>

        <!-- New User -->
        <div class="text-on-surface/60 text-center text-sm">
            New User? <a class="text-primary hover:text-primary/80 font-medium transition-colors"
               href="{{ route('register') }}">Sign Up</a>
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
