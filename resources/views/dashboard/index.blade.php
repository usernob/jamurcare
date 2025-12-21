<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        window.monitoring = {
            device_ulid: "{{ $current_device->ulid }}",
        };

        async function logout() {
            await axios.post("{{ route('logout') }}");
            location.reload();
        }
    </script>
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/dashboard/index.js')
</head>

<body class="bg-background text-on-surface font-poppins p-6 w-full h-screen min-w-xl">
    <section class="flex w-full h-full gap-4">
        <aside
            class="sticky flex flex-col gap-10 p-4 rounded-xl bg-surface text-on-surface h-full w-[500px] border border-outline shadow-lg">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo-cropped.png') }}" class="size-12 bg-primary dark:bg-surface rounded-full"
                    alt="Logo">
                <h2 class="font-baloo font-semibold text-2xl">{{ config('app.name', 'Laravel') }}</h2>
            </div>

            <div class="flex items-center gap-4">
                <img src="{{ $user->getAvatarUrlAttribute() }}"
                    class="w-14 border-2 border-on-primary rounded-full object-cover object-center bg-primary"
                    alt="Logo">
                <div>
                    <h2 class="font-semibold">Hello {{ $user->name }}</h2>
                    <p class="text-on-surface/60">Get Ready to plant</p>
                </div>
            </div>

            <div class="overflow-y-scroll h-full flex flex-col gap-4 scrollable">
                <div>
                    <h2 class="font-semibold mb-4 text-lg">Device</h2>
                    <div class="bg-surface-container rounded-xl w-full flex flex-col overflow-hidden"
                        id="dropdown-device">
                        <div class="flex items-center justify-between gap-4 px-4 py-2 cursor-pointer"
                            data-dropdown-trigger>
                            <div>
                                <h2 class="font-semibold">{{ $current_device->name }}</h2>
                                <h3 class="text-sm">Status: <span id="device-status">Loading...</span></h3>
                            </div>

                            <i
                                class="material-symbols-outlined !text-4xl rotate-180 arrow-drop transition-all duration-400 ease-out">
                                arrow_drop_up
                            </i>
                        </div>

                        <div class="flex flex-col border-t border-on-surface/20 dark:border-outline/60 divide-y divide-on-surface/20 dark:divide-outline/60 w-full transition-all ease-out"
                            data-dropdown-menu>
                            @foreach ($user->devices as $device)
                                @if ($device->id != $current_device->id)
                                    <a class="px-4 py-2 hover:bg-black/20 cursor-pointer"
                                        href="{{ route('dashboard.index', $device->ulid) }}">
                                        <h2>{{ $device->name }}</h2>
                                    </a>
                                @endif
                            @endforeach
                            <a class="px-4 py-2 hover:bg-black/20 cursor-pointer"
                                href="{{ route('device.add.form') }}">
                                <div class="flex items-center gap-1">
                                    <h2 class="material-symbols-outlined !text-xl">add</h2>
                                    <h2>Add Device</h2>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-semibold mb-4 text-lg">Auto Scenes</h2>
                    <div class="flex flex-col items-center gap-2 w-full">
                        <div class="bg-surface-container rounded-[1.625rem] w-full overflow-hidden" id="pump-state"
                            data-state="pump" data-dropdown>
                            <div class="relative flex items-center gap-4 p-1 pr-4 cursor-pointer" data-dropdown-trigger>
                                <div class="bg-primary-container rounded-full size-12 flex items-center justify-center">
                                    <i class="material-symbols-outlined text-primary !text-3xl">water_drop</i>
                                </div>
                                <h2 class="font-semibold">Irigation</h2>
                                <div class="grow"></div>
                                <div class="font-semibold state-label"></div>
                                <div
                                    class="state-load hidden absolute top-1/2 -translate-y-1/2 right-3 flex animate-spin">
                                    <i class="material-symbols-outlined">cached</i>
                                </div>
                            </div>
                            <div data-dropdown-menu
                                class="flex flex-col border-t border-on-surface/20 dark:border-outline/60 divide-y divide-on-surface/20 dark:divide-outline/60 transition-all ease-out">
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">OFF</h2>
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">ON</h2>
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">AUTO</h2>
                            </div>
                        </div>

                        <div class="bg-surface-container rounded-[1.625rem] w-full overflow-hidden" id="lamp-state"
                            data-state="lamp" data-dropdown>
                            <div class="relative flex items-center gap-4 p-1 pr-4 cursor-pointer" data-dropdown-trigger>
                                <div class="bg-primary-container rounded-full size-12 flex items-center justify-center">
                                    <i class="material-symbols-outlined text-primary !text-3xl">lightbulb_2</i>
                                </div>
                                <h2 class="font-semibold">Lamp</h2>
                                <div class="grow"></div>
                                <div class="font-semibold state-label"></div>
                                <div
                                    class="state-load hidden absolute top-1/2 -translate-y-1/2 right-3 flex animate-spin">
                                    <i class="material-symbols-outlined">cached</i>
                                </div>
                            </div>
                            <div data-dropdown-menu
                                class="flex flex-col border-t border-on-surface/20 dark:border-outline/60 divide-y divide-on-surface/20 dark:divide-outline/60 transition-all ease-out">
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">OFF</h2>
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">ON</h2>
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">AUTO</h2>
                            </div>
                        </div>

                        <div class="bg-surface-container rounded-[1.625rem] w-full overflow-hidden" id="fan-state"
                            data-state="fan" data-dropdown>
                            <div class="relative flex items-center gap-4 p-1 pr-4 cursor-pointer" data-dropdown-trigger>
                                <div class="bg-primary-container rounded-full size-12 flex items-center justify-center">
                                    <i class="material-symbols-outlined text-primary !text-3xl">toys_fan</i>
                                </div>
                                <h2 class="font-semibold">Fan</h2>
                                <div class="grow"></div>
                                <div class="font-semibold state-label"></div>
                                <div
                                    class="state-load hidden absolute top-1/2 -translate-y-1/2 right-3 flex animate-spin">
                                    <i class="material-symbols-outlined">cached</i>
                                </div>
                            </div>
                            <div data-dropdown-menu
                                class="flex flex-col border-t border-on-surface/20 dark:border-outline/60 divide-y divide-on-surface/20 dark:divide-outline/60 transition-all ease-out">
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">OFF</h2>
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">ON</h2>
                                <h2 class="px-4 py-2 hover:bg-black/20 cursor-pointer font-semibold">AUTO</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button class="material-symbols-outlined !text-3xl cursor-pointer" onclick="logout()">logout</button>
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
                        <h2 id="temperature-label">0</h2>
                        <h2>&#176;C</h2>
                    </div>
                </div>
                <canvas id="temperature-chart" class="w-full h-64"></canvas>
            </div>
            <div class="w-1/2 rounded-xl border border-outline p-4 bg-surface shadow-lg">
                <div class="flex justify-between">
                    <h2 class="font-semibold text-lg">Humididty</h2>
                    <div class="font-semibold text-5xl flex gap-2">
                        <h2 id="humidity-label">0</h2>
                        <h2>%</h2>
                    </div>
                </div>
                <canvas id="humidity-chart" class="w-full h-64"></canvas>
            </div>
        </div>
    </section>
</body>

</html>
