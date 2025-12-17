{{-- resources/views/auth/layouts/base.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/auth-common.js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-login-left {
            background-image: url("{{ asset('img/background-login-left.png') }}");
        }

        .bg-login-right {
            background-image: url("{{ asset('img/background-login-right.png') }}");
        }
    </style>
</head>

<body class="font-poppins antialiased bg-background text-on-surface">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Image -->
        <div class="lg:w-1/2 hidden lg:block lg:h-screen relative overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-login-left">
            </div>
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute top-0 left-6 z-10">
                <img src="{{ asset('img/logo-full.png') }}" alt="Jamur Care Logo" class="h-40">
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <h1 class="text-2xl sm:text-3xl font-baloo font-bold">@yield('hero-title')</h1>
                <p class="mt-2 text-sm sm:text-base opacity-90">@yield('hero-subtitle')</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="lg:w-1/2 w-full flex items-center justify-center p-6 sm:p-8 lg:p-12 bg-login-right">
            <div class="w-full max-w-md bg-surface rounded-xl border border-outline shadow-lg p-6 sm:p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-on-surface">@yield('form-title')</h2>
                    @hasSection('form-subtitle')
                        <p class="mt-2 text-sm text-on-surface/60">@yield('form-subtitle')</p>
                    @endif
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    @yield('scripts')
</body>

</html>
