<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Monitoring;
use App\Models\MonitoringSummary;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@jamurcare',
        ]);

        $device = $user->devices()->create(["ulid" => "01kc4kqwvwwt6xc7nrhq595q79", "name" => "dummy_device"]);
        $temp = 30;
        $hum = 60;
        $timestamp = now()->subDays(1);
        $now = now();

        while ($timestamp < $now) {
            $timestamp = $timestamp->addSeconds(5);
            $temp += rand(-1, 1) * 0.1;
            $hum += rand(-1, 1) * 0.1;

            Monitoring::factory()->for($device)->create([
                'temperature' => $temp,
                'humidity' => $hum,
                'recorded_at' => $timestamp,
            ]);
        }

        $monitorings = Monitoring::where('device_id', $device->id)
            ->orderBy('recorded_at')
            ->get()
            ->groupBy(function ($item) {
                $day = $item->recorded_at->day;

                return $item->recorded_at
                    ->copy()
                    ->setDay($day)
                    ->setHour(0)
                    ->setMinute(0)
                    ->setSecond(0)
                    ->toDateTimeString();
            });

        foreach ($monitorings as $periodStart => $items) {
            MonitoringSummary::create([
                'device_id' => $device->id,
                'period_start' => $periodStart,
                'avg_temperature' => round($items->avg('temperature'), 2),
                'avg_humidity' => round($items->avg('humidity'), 2),
                'data_count' => $items->count(),
            ]);
        }
    }
}
