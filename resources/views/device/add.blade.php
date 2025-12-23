<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Add Device</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-on-surface font-poppins w-screen h-screen">
    <section class="container p-4 h-full mx-auto flex items-center justify-center">
        <form class="w-full max-w-[30rem] grow rounded-xl bg-surface shadow-lg p-4 mb-20" method="POST"
            action="{{ route('device.add') }}">
            @csrf
            <h2 class="text-2xl font-bold">Name Your New Device</h2>
            <p class="mb-6">Please give your new device a name. (min 3 letters)</p>
            <input type="text" name="device-name" id="device-name"
                class="mt-3 w-full px-4 py-3 bg-surface dark:bg-surface-container outline-0 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 @error('device-name') ring-red-500 @enderror"
                placeholder="My New Awesome Device" required>
            @error('device-name')
                <div class="text-red-500">{{ $message }}</div>
            @enderror
            <button type="submit"
                class="bg-primary text-surface w-full px-2 py-3 rounded-lg text-lg font-semibold mt-6 cursor-pointer">
                Create Device
            </button>
        </form>
    </section>
</body>

</html>
