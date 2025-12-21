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

<body class="bg-background text-on-surface font-poppins p-4 md:p-6 w-full h-screen min-w-[320px]">
    <section class="flex flex-col xl:flex-row w-full h-full gap-4">
        <!-- ========== SIDEBAR (100% SAMA DENGAN KODE ASLIMU) ========== -->
        <aside
            class="sticky flex flex-col gap-6 p-4 rounded-xl bg-surface text-on-surface h-full xl:w-[500px] border border-outline shadow-lg">
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
                            <div class="bg-surface-container rounded-[1.625rem] w-full overflow-hidden"
                                id="{{ $scene }}-state" data-state="{{ $scene }}" data-dropdown>
                                <div class="relative flex items-center gap-4 p-1 pr-4 cursor-pointer"
                                    data-dropdown-trigger>
                                    <div
                                        class="bg-primary-container rounded-full size-12 flex items-center justify-center">
                                        <i
                                            class="material-symbols-outlined text-primary !text-3xl">
                                            @if ($scene === 'pump') water_drop
                                            @elseif ($scene === 'lamp') lightbulb_2
                                            @else toys_fan
                                            @endif
                                        </i>
                                    </div>
                                    <h2 class="font-semibold">
                                        @if ($scene === 'pump') Irigation
                                        @elseif ($scene === 'lamp') Lamp
                                        @else Fan
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

            <div class="flex items-center justify-between pt-2 mt-auto">
                <button class="material-symbols-outlined !text-3xl cursor-pointer" onclick="logout()">logout</button>
                <div class="relative size-8 cursor-pointer" id="darkmode-toggler">
                    <span class="absolute material-symbols-outlined !text-3xl !hidden" id="icon-dark">dark_mode</span>
                    <span class="absolute material-symbols-outlined !text-3xl !hidden" id="icon-light">light_mode</span>
                </div>
            </div>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <div class="w-full h-fit flex flex-col gap-4">
            <!-- Real-time Monitoring (2 kolom di desktop) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Suhu -->
                <div class="rounded-xl border border-outline p-4 bg-surface shadow-lg">
                    <div class="flex justify-between">
                        <h2 class="font-semibold text-lg">Air Temperature</h2>
                        <div class="font-semibold text-4xl md:text-5xl flex gap-2">
                            <h2 id="temperature-label">0</h2>
                            <h2>&#176;C</h2>
                        </div>
                    </div>
                    <canvas id="temperature-chart" class="w-full h-64 mt-4"></canvas>
                </div>

                <!-- Kelembapan -->
                <div class="rounded-xl border border-outline p-4 bg-surface shadow-lg">
                    <div class="flex justify-between">
                        <h2 class="font-semibold text-lg">Humidity</h2>
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
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggler = document.getElementById('darkmode-toggler');
            const darkIcon = document.getElementById('icon-dark');
            const lightIcon = document.getElementById('icon-light');

            const isDark = localStorage.getItem('theme') === 'dark' ||
                (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                darkIcon.classList.remove('hidden');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                lightIcon.classList.remove('hidden');
                document.documentElement.setAttribute('data-theme', 'light');
            }

            toggler.addEventListener('click', function () {
                const isNowDark = darkIcon.classList.contains('hidden');
                if (isNowDark) {
                    darkIcon.classList.remove('hidden');
                    lightIcon.classList.add('hidden');
                    document.documentElement.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    lightIcon.classList.remove('hidden');
                    darkIcon.classList.add('hidden');
                    document.documentElement.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                }
            });

            document.querySelectorAll('[data-dropdown-trigger]').forEach(trigger => {
                trigger.addEventListener('click', function () {
                    const container = this.closest('[data-dropdown]');
                    const menu = container ? container.querySelector('[data-dropdown-menu]') : 
                                  document.querySelector('#dropdown-device [data-dropdown-menu]');
                    const arrow = this.querySelector('.arrow-drop');
                    if (menu && arrow) {
                        const isExpanded = !menu.classList.contains('hidden');
                        arrow.textContent = isExpanded ? 'expand_more' : 'expand_less';
                    }
                });
            });

            function updateAIInsight() {
                const tempEl = document.getElementById('temperature-label');
                const humEl = document.getElementById('humidity-label');

                if (!tempEl || !humEl) return;

                const tempText = tempEl.textContent.trim();
                const humText = humEl.textContent.trim();

                const temp = parseFloat(tempText);
                const hum = parseFloat(humText);

                if (isNaN(temp) || isNaN(hum)) {
                    document.getElementById('ai-insight').textContent = 'Menunggu data dari sistem IoT...';
                    return;
                }

                let insight = "";
                if (temp > 32 && hum < 45) {
                    insight = "Suhu sangat tinggi & kelembapan rendah. Sistem telah meningkatkan irigasi.";
                } else if (temp < 18 && hum > 85) {
                    insight = "Suhu rendah & kelembapan tinggi. Risiko jamur meningkat.";
                } else if (temp >= 22 && temp <= 28 && hum >= 60 && hum <= 75) {
                    insight = "Kondisi optimal! Tanaman berada dalam zona nyaman.";
                } else if (temp > 28 && hum > 80) {
                    insight = "Suhu & kelembapan tinggi. Kipas otomatis diaktifkan.";
                } else {
                    insight = "Kondisi stabil. Sistem terus memantau untuk optimasi real-time.";
                }

                document.getElementById('ai-insight').textContent = insight;
            }

            // Jalankan pertama kali
            updateAIInsight();

            // Update setiap 5 detik (sesuaikan dengan kecepatan update IoT-mu)
            setInterval(updateAIInsight, 5000);
        });
    </script>
</body>

</html>