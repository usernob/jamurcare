<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - New Device</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        function copyUlid() {
            const text = document.getElementById('device-ulid').innerText;

            navigator.clipboard.writeText(text)
                .then(() => {
                    alert('Berhasil dicopy!');
                })
                .catch(() => {
                    alert('Gagal copy');
                });
        }
    </script>
</head>

<body class="bg-background text-on-surface font-poppins p-6 w-full h-screen min-w-xl">
    <section class="container h-full mx-auto flex items-center justify-center">
        <div class="max-w-[30rem] grow rounded-xl bg-surface shadow-lg p-4 mb-20">
            <h2 class="text-2xl font-bold">Success Create Device</h2>
            <p class="mb-6">Device <span class="font-semibold">{{ $device->name }}</span> created successfuly. Here
                your new device ulid. <a href="#" class="text-primary">How to setup?</a></p>
            <div class="flex text-xl px-2 py-4 rounded-lg outline-2 outline-outline">
                <h2 id="device-ulid" class="grow text-center font-semibold">{{ $device->ulid }}</h2>
                <button class="material-symbols-outlined cursor-pointer" onclick="copyUlid()">content_copy</button>
            </div>
            <a href="{{ route('dashboard.index', ['ulid' => $device->ulid]) }}"
                class="block bg-primary text-surface text-center w-full px-2 py-3 rounded-lg text-lg font-semibold mt-6 cursor-pointer">
                Go To Dashboard
            </a>
        </div>
    </section>
</body>

</html>
