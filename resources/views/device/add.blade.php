<!DOCTYPE html>
<html data-theme="dark"
      lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <link type="image/x-icon"
          href="{{ asset('img/logo-cropped.ico') }}"
          rel="icon">
    <title>{{ config('app.name', 'Laravel') }} - Add Device</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-on-surface font-poppins h-screen w-screen">
    <section class="container mx-auto flex h-full items-center justify-center p-4">
        <form class="bg-surface mb-20 w-full max-w-[30rem] grow rounded-xl p-4 shadow-lg"
              method="POST"
              action="{{ route('device.add') }}">
            @csrf
            <h2 class="text-2xl font-bold">Name Your New Device</h2>
            <p class="mb-6">Please give your new device a name. (min 3 letters)</p>
            <input class="bg-surface dark:bg-surface-container border-outline focus:ring-primary @error('device-name') ring-red-500 @enderror mt-3 w-full rounded-lg border px-4 py-3 outline-0 transition-all duration-200 focus:border-transparent focus:ring-2"
                   id="device-name"
                   name="device-name"
                   type="text"
                   placeholder="My New Awesome Device"
                   required>
            @error('device-name')
                <div class="text-red-500">{{ $message }}</div>
            @enderror
            <button class="bg-primary text-surface mt-6 w-full cursor-pointer rounded-lg px-2 py-3 text-lg font-semibold"
                    type="submit">
                Create Device
            </button>
        </form>
    </section>
</body>

</html>
