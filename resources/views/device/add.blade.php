<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Add Device</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-on-surface font-poppins p-6 w-full h-screen min-w-xl">
    <section class="container h-full mx-auto flex items-center justify-center">
        <form class="max-w-[30rem] grow rounded-xl bg-surface shadow-lg p-4 mb-20" method="POST">
            @csrf
            <h2 class="text-2xl font-bold">Name Your New Device</h2>
            <p class="mb-6">Please give your new device a name. (min 3 letters)</p>
            <input type="text" name="device-name" id="device-name"
                class="w-full outline-2 @error('device-name') outline-red-500 @else outline-outline @enderror focus:outline-on-surface rounded-lg px-2 py-4 text-lg"
                placeholder="My New Awesome Device" required>
            @error('device-name')
                <div class="text-red-500">{{ $message }}</div>
            @enderror
            <button type="submit"
                class="bg-primary text-surface w-full px-2 py-3 rounded-lg text-lg font-semibold mt-6 cursor-pointer">Create
                Device</button>
        </form>
    </section>
</body>

</html>
