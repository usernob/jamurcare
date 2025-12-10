<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Monitoring;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = Device::factory()->count(2)->create();
        $temp = 25;
        $hum = 60;
        foreach ($devices as $device) {
            $timestamp = now()->subDays(7);

            for ($i = 0; $i < 1000; $i++) {
                $timestamp = $timestamp->addMinute(5);
                $temp += rand(-3, 3) * 0.1;
                $hum += rand(-3, 3) * 0.1;

                Monitoring::factory()->for($device)->create([
                    'temperature' => $temp,
                    'humidity' => $hum,
                    'recorded_at' => $timestamp->addSeconds(rand(30, 70)),
                ]);
            }
        }
    }
}
