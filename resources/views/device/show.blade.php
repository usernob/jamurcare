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

<body class="bg-background text-on-surface font-poppins h-screen w-screen">
    <section class="container mx-auto flex h-full items-center justify-center p-4">
        <div class="bg-surface mb-20 w-full max-w-[30rem] grow rounded-xl p-4 shadow-lg">
            <h2 class="text-2xl font-bold">Success Create Device</h2>
            <p class="mb-6">Device <span class="font-semibold">{{ $device->name }}</span> created successfuly. Here
                your new device ulid. <a class="text-primary"
                   href="#">How to setup?</a></p>
            <div class="outline-outline flex w-full items-center rounded-lg p-2 text-xl outline-2">
                <h2 class="grow overflow-x-hidden text-center font-semibold"
                    id="device-ulid">{{ $device->ulid }}</h2>
                <button class="material-symbols-outlined cursor-pointer p-2"
                        onclick="copyUlid()">content_copy</button>
            </div>
            <a class="bg-primary text-surface mt-6 block w-full cursor-pointer rounded-lg px-2 py-3 text-center text-lg font-semibold"
               href="{{ route('dashboard.index', ['ulid' => $device->ulid]) }}">
                Go To Dashboard
            </a>
        </div>
    </section>
</body>

</html>
