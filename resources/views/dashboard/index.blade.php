<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        window.monitoring = {
            device_ulid: "{{ $ulid }}",
            // userId: {{ auth()->id() }},
        };
    </script>
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/dashboard/index.js')
</head>

<body class="bg-background text-on-surface font-poppins p-6 w-screen h-screen min-w-xl">
    <section class="flex w-full h-full gap-4">
        <aside
            class="sticky flex flex-col gap-10 p-4 rounded-[20px] bg-primary text-on-primary dark:bg-surface dark:text-on-surface min-h-full h-fit w-[500px]">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo-cropped.png') }}" class="w-12" alt="Logo">
                <h2 class="font-baloo font-semibold text-2xl">{{ config('app.name', 'Laravel') }}</h2>
            </div>

            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo-cropped.png') }}"
                    class="w-14 border border-on-primary rounded-full object-cover object-center" alt="Logo">
                <div>
                    <h2 class="font-semibold">Hello Sutan</h2>
                    <p class="text-on-primary/60">Get Ready to plant</p>
                </div>
            </div>

            <div>
                <h2 class="font-semibold mb-4 text-lg">Auto Scenes</h2>
                <div class="flex flex-col items-center gap-2 w-full">
                    <div class="flex items-center gap-4 bg-surface-container rounded-full p-1 w-full">
                        <div class="bg-primary-container rounded-full size-12 flex items-center justify-center">
                            <i class="material-symbols-outlined text-primary !text-3xl">water_drop</i>
                        </div>
                        <h2 class="font-semibold">Auto Irigation</h2>
                        <div class="grow"></div>
                        <div class="flex">
                            <input type="checkbox" name="auto-irigation" id="auto-irigation" class="hidden">
                            <label for="auto-irigation" class="toggle"></label>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-surface-container rounded-full p-1 w-full">
                        <div class="bg-primary-container rounded-full size-12 flex items-center justify-center">
                            <i class="material-symbols-outlined text-primary !text-3xl">lightbulb_2</i>
                        </div>
                        <h2 class="font-semibold">Auto Lamp</h2>
                        <div class="grow"></div>
                        <div class="flex">
                            <input type="checkbox" name="auto-lamp" id="auto-lamp" class="hidden">
                            <label for="auto-lamp" class="toggle"></label>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-surface-container rounded-full p-1 w-full">
                        <div class="bg-primary-container rounded-full size-12 flex items-center justify-center">
                            <i class="material-symbols-outlined text-primary !text-3xl">toys_fan</i>
                        </div>
                        <h2 class="font-semibold">Auto Fan</h2>
                        <div class="grow"></div>
                        <div class="flex">
                            <input type="checkbox" name="auto-fan" id="auto-fan" class="hidden">
                            <label for="auto-fan" class="toggle"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="font-semibold mb-4 text-lg">Settings</h2>
                <div class="grid grid-cols-2 auto-cols-max auto-row-max gap-2">
                    <div class="bg-surface-container rounded-2xl flex gap-4 items-center py-2 px-4">
                        <i class="material-symbols-outlined !text-3xl">water_drop</i>
                        <div>
                            <h2 class="font-semibold mb-2">Irigation</h2>
                            <p class="text-sm">MIN 12.09</p>
                        </div>
                    </div>
                    <div class="bg-surface-container rounded-2xl flex gap-4 items-center py-2 px-4">
                        <i class="material-symbols-outlined !text-3xl">lightbulb_2</i>
                        <div>
                            <h2 class="font-semibold mb-2">Irigation</h2>
                            <p class="text-sm">MIN 12.09</p>
                        </div>
                    </div>
                    <div class="bg-surface-container rounded-2xl flex gap-4 items-center py-2 px-4">
                        <i class="material-symbols-outlined !text-3xl">toys_fan</i>
                        <div>
                            <h2 class="font-semibold mb-2">Irigation</h2>
                            <p class="text-sm">MIN 12.09</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grow"></div>

            <div class="flex items-center justify-between">
                <span class="material-symbols-outlined !text-3xl">logout</span>
                <div class="relative size-8 cursor-pointer" id="darkmode-toggler">
                    <span class="absolute material-symbols-outlined !text-3xl !hidden" id="icon-dark">dark_mode</span>
                    <span class="absolute material-symbols-outlined !text-3xl !hidden" id="icon-light">light_mode</span>
                </div>
            </div>
        </aside>

        <div class="w-full h-fit flex gap-4">
            <div class="w-1/2 rounded-xl border border-outline p-4 bg-surface shadow-lg">
                <div class="flex justify-between">
                    <h2 class="font-semibold text-lg">Air Temperature</h2>
                    <div class="font-semibold text-5xl flex gap-2">
                        <h2 id="temperature-label"></h2>
                        <h2>&#176;C</h2>
                    </div>
                </div>
                <canvas id="temperature-chart" class="w-full h-64"></canvas>
            </div>
            <div class="w-1/2 rounded-xl border border-outline p-4 bg-surface shadow-lg">
                <div class="flex justify-between">
                    <h2 class="font-semibold text-lg">Humididty</h2>
                    <div class="font-semibold text-5xl flex gap-2">
                        <h2 id="humidity-label"></h2>
                        <h2>%</h2>
                    </div>
                </div>
                <canvas id="humidity-chart" class="w-full h-64"></canvas>
            </div>
        </div>
    </section>
</body>

</html>
