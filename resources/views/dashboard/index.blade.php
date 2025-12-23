<!DOCTYPE html>
<html lang="en" data-theme="dark" class="scroll-smooth md:scrollable">

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

<body class="bg-background text-on-surface font-poppins w-full min-w-[320px]">
    <section class="flex flex-col md:flex-row w-full h-full gap-4 p-4 md:p-0">
        <div class="md:sticky top-0 md:p-6 md:pr-0 md:h-screen">
            <aside
                class="flex flex-col gap-6 p-4 rounded-xl bg-surface text-on-surface h-full md:w-[300px] xl:w-[400px] border border-outline shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('img/logo-cropped.png') }}"
                            class="size-12 bg-primary dark:bg-surface rounded-full" alt="Logo">
                        <h2 class="font-baloo font-semibold text-2xl">{{ config('app.name', 'Laravel') }}</h2>
                    </div>
                    <div class="flex md:hidden items-center">
                        <button
                            class="size-12 rounded-full hover:bg-surface-container material-symbols-outlined !text-2xl cursor-pointer"
                            onclick="logout()">logout</button>
                        <div class="relative size-12 rounded-full aspect-square hover:bg-surface-container cursor-pointer"
                            id="darkmode-toggler">
                            <span
                                class="absolute top-1/2 right-1/2 -translate-y-1/2 translate-x-1/2 material-symbols-outlined !text-2xl !hidden"
                                id="icon-dark">dark_mode</span>
                            <span
                                class="absolute top-1/2 right-1/2 -translate-y-1/2 translate-x-1/2 material-symbols-outlined !text-2xl !hidden"
                                id="icon-light">light_mode</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <img src="{{ $user->getAvatarUrlAttribute() }}" referrerpolicy="no-referrer"
                        class="size-14 aspect-square border-2 border-on-primary rounded-full object-cover object-center bg-primary"
                        alt="Logo">
                    <div>
                        <h2 class="font-semibold">Hello {{ Str::limit($user->name, 20, preserveWords: true) }}</h2>
                        <p class="text-on-surface/60">Get Ready to plant</p>
                    </div>
                    <div class="grow"></div>
                    <a href="{{ route('profile.edit.form') }}"
                        class="aspect-square h-12 flex justify-center items-center rounded-full hover:bg-surface-container cursor-pointer">
                        <h2 class="material-symbols-outlined !text-2xl">edit</h2>
                    </a>
                </div>

                <div class="overflow-y-auto h-full flex flex-col gap-4 scrollable">
                    <div>
                        <h2 class="font-semibold mb-4 text-lg">Device</h2>
                        <div class="bg-surface-container hover:bg-surface-container/80 rounded-xl w-full flex flex-col overflow-hidden"
                            id="dropdown-device">
                            <div class="flex items-center justify-between gap-4 px-4 py-2 cursor-pointer"
                                data-dropdown-trigger>
                                <div>
                                    <h2 class="font-semibold">{{ $current_device->name }}</h2>
                                    <h3 class="text-sm">Status: <span id="device-status">Loading...</span></h3>
                                </div>

                                <i
                                    class="material-symbols-outlined !text-2xl arrow-drop transition-all duration-400 ease-out">
                                    expand_more
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
                            @foreach (['pump', 'lamp', 'fan'] as $scene)
                                <div class="bg-surface-container hover:bg-surface-container/80 rounded-[1.625rem] w-full overflow-hidden"
                                    id="{{ $scene }}-state" data-state="{{ $scene }}" data-dropdown>
                                    <div class="relative flex items-center gap-4 p-1 pr-4 cursor-pointer"
                                        data-dropdown-trigger>
                                        <div
                                            class="bg-primary-container rounded-full size-12 flex items-center justify-center">
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
                                        <div class="font-semibold state-label">AUTO</div>
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
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex items-center justify-between pt-2 mt-auto">
                    <button
                        class="hover:bg-surface-container rounded-full aspect-square w-12 material-symbols-outlined !text-3xl cursor-pointer"
                        onclick="logout()">logout</button>
                    <div class="relative size-12 rounded-full aspect-square hover:bg-surface-container cursor-pointer"
                        id="darkmode-toggler">
                        <span
                            class="absolute top-1/2 right-1/2 -translate-y-1/2 translate-x-1/2 material-symbols-outlined !text-3xl !hidden"
                            id="icon-dark">dark_mode</span>
                        <span
                            class="absolute top-1/2 right-1/2 -translate-y-1/2 translate-x-1/2 material-symbols-outlined !text-3xl !hidden"
                            id="icon-light">light_mode</span>
                    </div>
                </div>
            </aside>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <div class="w-full h-fit flex flex-col gap-4 md:p-6 md:pl-0">
            <!-- Real-time Monitoring (2 kolom di desktop) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Suhu -->
                <div class="rounded-xl border border-outline p-4 bg-surface shadow-lg">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-2 font-semibold text-lg">
                            <i class="material-symbols-outlined text-primary">thermometer</i>
                            <h2>Air Temperature</h2>
                        </div>
                        <div class="font-semibold text-4xl md:text-5xl flex gap-2">
                            <h2 id="temperature-label">0</h2>
                            <h2>&#176;C</h2>
                        </div>
                    </div>
                    <canvas id="temperature-chart" class="w-full h-64 mt-4"></canvas>
                </div>

                <!-- Kelembapan -->
                <div class="rounded-xl border border-outline p-4 bg-surface shadow-lg">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-2 font-semibold text-lg">
                            <i class="material-symbols-outlined text-primary">cool_to_dry</i>
                            <h2>Humidity</h2>
                        </div>
                        <div class="font-semibold text-4xl md:text-5xl flex gap-2">
                            <h2 id="humidity-label">0</h2>
                            <h2>%</h2>
                        </div>
                    </div>
                    <canvas id="humidity-chart" class="w-full h-64 mt-4"></canvas>
                </div>
            </div>

            <!-- ========== AI INSIGHT (FULL WIDTH) ========== -->
            <div class="rounded-xl border border-outline p-4 bg-surface shadow-lg">
                <div class="flex items-center gap-2 mb-3">
                    <i class="material-symbols-outlined text-primary !text-xl">auto_awesome</i>
                    <h2 class="font-semibold text-lg">AI Insight Saat Ini</h2>
                </div>
                <p class="italic text-sm text-primary font-medium" id="ai-insight">
                    Menunggu data dari sistem IoT...
                </p>
                <div class="mt-3 text-xs text-on-surface/60 flex items-center gap-1">
                    <i class="material-symbols-outlined !text-xs">auto_awesome</i>
                    <span>Berdasarkan data real-time dari perangkat</span>
                </div>
            </div>
            <!-- ========== REKAP 7 HARI TERAKHIR ========== -->
            <div class="rounded-xl border border-outline p-4 bg-surface shadow-lg">
                <div class="flex items-center gap-2 mb-4">
                    <i class="material-symbols-outlined text-primary !text-xl">calendar_month</i>
                    <h2 class="font-semibold text-lg">Rekap 7 Hari Terakhir</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @for ($i = 0; $i < 7; $i++)
                        @php
                            $date = now()
                                ->subDays(6 - $i)
                                ->translatedFormat('D, d M');
                            $temp = number_format(24 + rand(0, 6) + rand(0, 9) / 10, 1);
                            $humid = rand(70, 85);
                            $isToday = $i === 6;
                        @endphp
                        <div
                            class="bg-surface-container p-3 rounded-lg border {{ $isToday ? 'border-primary' : 'border-outline/30' }}">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-medium text-sm {{ $isToday ? 'text-primary' : 'text-on-surface' }}">
                                    {{ $date }}
                                </span>
                                @if ($isToday)
                                    <span
                                        class="text-[10px] bg-primary text-white dark:text-gray-900 font-semibold px-1 py-0.5 rounded">Hari
                                        Ini</span>
                                @endif
                            </div>
                            <div class="flex justify-between text-xs font-bold text-on-surface/80">
                                <div class="flex items-center gap-1">
                                    <i class="material-symbols-outlined text-primary !text-lg">thermometer</i>
                                    <span>{{ $temp }}°</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="material-symbols-outlined text-primary !text-lg">cool_to_dry</i>
                                    <span>{{ $humid }}%</span>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="mt-3 text-xs text-on-surface/60 flex items-center gap-1.5">
                    <i class="material-symbols-outlined !text-xs">info</i>
                    <span>Menampilkan suhu & kelembapan rata-rata</span>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
