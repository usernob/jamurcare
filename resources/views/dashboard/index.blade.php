<!DOCTYPE html>
<html class="md:scrollable scroll-smooth"
      data-theme="dark"
      lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <link type="image/x-icon"
          href="{{ asset('img/logo-cropped.ico') }}"
          rel="icon">
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

<body class="bg-background text-on-surface font-poppins w-full min-w-[320px]">
    <section class="flex h-full w-full flex-col gap-4 p-4 md:flex-row md:p-0">
        <div class="top-0 md:sticky md:h-screen md:p-6 md:pr-0">
            <aside
                   class="bg-surface text-on-surface border-outline flex h-full flex-col gap-6 rounded-xl border p-4 shadow-lg md:w-[300px] xl:w-[400px]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img class="bg-primary dark:bg-surface size-12 rounded-full"
                             src="{{ asset('img/logo-cropped.png') }}"
                             alt="Logo">
                        <h2 class="font-baloo text-2xl font-semibold">{{ config('app.name', 'Laravel') }}</h2>
                    </div>
                    <div class="flex items-center md:hidden">
                        <button class="hover:bg-surface-container material-symbols-outlined size-12 cursor-pointer rounded-full !text-2xl"
                                onclick="logout()">logout</button>
                        <div class="hover:bg-surface-container relative aspect-square size-12 cursor-pointer rounded-full"
                             id="darkmode-toggler">
                            <span class="material-symbols-outlined absolute right-1/2 top-1/2 !hidden -translate-y-1/2 translate-x-1/2 !text-2xl"
                                  id="icon-dark">dark_mode</span>
                            <span class="material-symbols-outlined absolute right-1/2 top-1/2 !hidden -translate-y-1/2 translate-x-1/2 !text-2xl"
                                  id="icon-light">light_mode</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <img class="border-on-primary bg-primary aspect-square size-14 rounded-full border-2 object-cover object-center"
                         src="{{ $user->getAvatarUrlAttribute() }}"
                         alt="Logo"
                         referrerpolicy="no-referrer">
                    <div>
                        <h2 class="font-semibold">Hello {{ Str::limit($user->name, 20, preserveWords: true) }}</h2>
                        <p class="text-on-surface/60">Get Ready to plant</p>
                    </div>
                    <div class="grow"></div>
                    <a class="hover:bg-surface-container flex aspect-square h-12 cursor-pointer items-center justify-center rounded-full"
                       href="{{ route('profile.edit.form') }}">
                        <h2 class="material-symbols-outlined !text-2xl">edit</h2>
                    </a>
                </div>

                <div class="scrollable flex h-full flex-col gap-4 overflow-y-auto">
                    <div>
                        <h2 class="mb-4 text-lg font-semibold">Device</h2>
                        <div class="bg-surface-container hover:bg-surface-container/80 flex w-full flex-col overflow-hidden rounded-xl"
                             id="dropdown-device">
                            <div class="flex cursor-pointer items-center justify-between gap-4 px-4 py-2"
                                 data-dropdown-trigger>
                                <div>
                                    <h2 class="font-semibold">{{ $current_device->name }}</h2>
                                    <h3 class="text-sm">Status: <span id="device-status">Loading...</span></h3>
                                </div>

                                <i
                                   class="material-symbols-outlined arrow-drop duration-400 !text-2xl transition-all ease-out">
                                    expand_more
                                </i>
                            </div>

                            <div class="border-on-surface/20 dark:border-outline/60 divide-on-surface/20 dark:divide-outline/60 flex w-full flex-col divide-y border-t transition-all ease-out"
                                 data-dropdown-menu>
                                @foreach ($user->devices as $device)
                                    @if ($device->id != $current_device->id)
                                        <a class="cursor-pointer px-4 py-2 hover:bg-black/20"
                                           href="{{ route('dashboard.index', $device->ulid) }}">
                                            <h2>{{ $device->name }}</h2>
                                        </a>
                                    @endif
                                @endforeach
                                <a class="cursor-pointer px-4 py-2 hover:bg-black/20"
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
                        <h2 class="mb-4 text-lg font-semibold">Auto Scenes</h2>
                        <div class="flex w-full flex-col items-center gap-2">
                            @foreach (['pump', 'lamp', 'fan'] as $scene)
                                <div class="bg-surface-container hover:bg-surface-container/80 w-full overflow-hidden rounded-[1.625rem]"
                                     id="{{ $scene }}-state"
                                     data-state="{{ $scene }}"
                                     data-dropdown>
                                    <div class="relative flex cursor-pointer items-center gap-4 p-1 pr-4"
                                         data-dropdown-trigger>
                                        <div
                                             class="bg-primary-container flex size-12 items-center justify-center rounded-full">
                                            <i class="material-symbols-outlined text-primary !text-3xl">
                                                @if ($scene === 'pump')
                                                    water_drop
                                                @elseif ($scene === 'lamp')
                                                    lightbulb_2
                                                @else
                                                    toys_fan
                                                @endif
                                            </i>
                                        </div>
                                        <h2 class="font-semibold">
                                            @if ($scene === 'pump')
                                                Irigation
                                            @elseif ($scene === 'lamp')
                                                Lamp
                                            @else
                                                Fan
                                            @endif
                                        </h2>
                                        <div class="grow"></div>
                                        <div class="state-label font-semibold">AUTO</div>
                                        <div
                                             class="state-load absolute right-3 top-1/2 flex hidden -translate-y-1/2 animate-spin">
                                            <i class="material-symbols-outlined">cached</i>
                                        </div>
                                    </div>
                                    <div class="border-on-surface/20 dark:border-outline/60 divide-on-surface/20 dark:divide-outline/60 flex flex-col divide-y border-t transition-all ease-out"
                                         data-dropdown-menu>
                                        <h2 class="cursor-pointer px-4 py-2 font-semibold hover:bg-black/20">OFF</h2>
                                        <h2 class="cursor-pointer px-4 py-2 font-semibold hover:bg-black/20">ON</h2>
                                        <h2 class="cursor-pointer px-4 py-2 font-semibold hover:bg-black/20">AUTO</h2>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-auto hidden items-center justify-between pt-2 md:flex">
                    <button class="hover:bg-surface-container material-symbols-outlined aspect-square w-12 cursor-pointer rounded-full !text-3xl"
                            onclick="logout()">logout</button>
                    <div class="hover:bg-surface-container relative aspect-square size-12 cursor-pointer rounded-full"
                         id="darkmode-toggler">
                        <span class="material-symbols-outlined absolute right-1/2 top-1/2 !hidden -translate-y-1/2 translate-x-1/2 !text-3xl"
                              id="icon-dark">dark_mode</span>
                        <span class="material-symbols-outlined absolute right-1/2 top-1/2 !hidden -translate-y-1/2 translate-x-1/2 !text-3xl"
                              id="icon-light">light_mode</span>
                    </div>
                </div>
            </aside>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <div class="flex h-fit w-full flex-col gap-4 md:p-6 md:pl-0">
            <!-- Real-time Monitoring (2 kolom di desktop) -->
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <!-- Suhu -->
                <div class="border-outline bg-surface rounded-xl border p-4 shadow-lg">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2 text-lg font-semibold">
                            <i class="material-symbols-outlined text-primary">thermometer</i>
                            <h2>Air Temperature</h2>
                        </div>
                        <div class="flex gap-2 text-4xl font-semibold md:text-5xl">
                            <h2 id="temperature-label">0</h2>
                            <h2>&#176;C</h2>
                        </div>
                    </div>
                    <canvas class="mt-4 h-64 w-full"
                            id="temperature-chart"></canvas>
                </div>

                <!-- Kelembapan -->
                <div class="border-outline bg-surface rounded-xl border p-4 shadow-lg">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2 text-lg font-semibold">
                            <i class="material-symbols-outlined text-primary">cool_to_dry</i>
                            <h2>Humidity</h2>
                        </div>
                        <div class="flex gap-2 text-4xl font-semibold md:text-5xl">
                            <h2 id="humidity-label">0</h2>
                            <h2>%</h2>
                        </div>
                    </div>
                    <canvas class="mt-4 h-64 w-full"
                            id="humidity-chart"></canvas>
                </div>
            </div>

            <!-- ========== AI INSIGHT (FULL WIDTH) ========== -->
            <div class="border-outline bg-surface rounded-xl border p-4 shadow-lg">
                <div class="mb-3 flex items-center gap-2">
                    <i class="material-symbols-outlined text-primary !text-xl">auto_awesome</i>
                    <h2 class="text-lg font-semibold">AI Insight Saat Ini</h2>
                </div>
                <p class="text-primary text-sm font-medium italic"
                   id="ai-insight">
                    Menunggu data dari sistem IoT...
                </p>
                <div class="text-on-surface/60 mt-3 flex items-center gap-1 text-xs">
                    <i class="material-symbols-outlined !text-xs">auto_awesome</i>
                    <span>Berdasarkan data real-time dari perangkat</span>
                </div>
            </div>
            <!-- ========== REKAP 7 HARI TERAKHIR ========== -->
            <div class="border-outline bg-surface rounded-xl border p-4 shadow-lg">
                <div class="mb-4 flex items-center gap-2">
                    <i class="material-symbols-outlined text-primary !text-xl">calendar_month</i>
                    <h2 class="text-lg font-semibold">Rekap 7 Hari Terakhir</h2>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3"
                     id="weekly-summary"></div>
                <template id="summary-card-template">
                    <div class="bg-surface-container border-outline/30 rounded-lg border p-3">
                        <div class="mb-2 flex items-start justify-between">
                            <span class="date text-on-surface text-sm font-medium"></span>
                            <span
                                  class="badge bg-primary hidden rounded px-1 py-0.5 text-[10px] font-semibold text-white dark:text-gray-900">
                                Hari Ini
                            </span>
                        </div>

                        <div class="text-on-surface/80 flex justify-between text-xs font-bold">
                            <div class="flex items-center gap-1">
                                <i class="material-symbols-outlined text-primary !text-lg">
                                    thermometer
                                </i>
                                <span class="temperature"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <i class="material-symbols-outlined text-primary !text-lg">
                                    cool_to_dry
                                </i>
                                <span class="humidity"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="text-on-surface/60 mt-3 flex items-center gap-1.5 text-xs">
                    <i class="material-symbols-outlined !text-xs">info</i>
                    <span>Menampilkan suhu & kelembapan rata-rata</span>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
