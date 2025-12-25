<!DOCTYPE html>
<html data-theme="light"
      lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <link type="image/x-icon"
          href="{{ asset('img/logo-cropped.ico') }}"
          rel="icon">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/auth-common.js')
    <style>
        .bg-login-left {
            background-image: url("{{ asset('img/background-login-left.png') }}");
        }

        .bg-login-right {
            background-image: url("{{ asset('img/background-login-right.png') }}");
        }
    </style>
</head>

<body class="font-poppins bg-background text-on-surface accent-primary antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">
        <!-- Left Side - Image -->
        <div class="relative hidden overflow-hidden lg:block lg:w-1/2">
            <div class="bg-login-left absolute inset-0 bg-cover bg-center bg-no-repeat">
            </div>
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute left-6 top-0 z-10">
                <img class="h-40"
                     src="{{ asset('img/logo-full.png') }}"
                     alt="Jamur Care Logo">
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <h1 class="font-baloo text-2xl font-bold sm:text-3xl">@yield('hero-title')</h1>
                <p class="mt-2 text-sm opacity-90 sm:text-base">@yield('hero-subtitle')</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="bg-login-right flex w-full grow items-center justify-center p-6 sm:p-8 lg:w-1/2 lg:p-12">
            <div class="bg-surface border-outline w-full max-w-md rounded-xl border p-6 shadow-lg sm:p-8">
                <div class="mb-8 text-center">
                    <h2 class="text-on-surface text-2xl font-bold">@yield('form-title')</h2>
                    @hasSection('form-subtitle')
                        <p class="text-on-surface/60 mt-2 text-sm">@yield('form-subtitle')</p>
                    @endif
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    @yield('scripts')
</body>

</html>
