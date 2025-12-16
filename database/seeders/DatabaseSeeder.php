<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Monitoring;
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
        $timestamp = now()->subDays(7);

        // for ($i = 0; $i < 1728; $i++) { // 1728 is estimated row for 6 days if interval update is 5 minute
        //     $timestamp = $timestamp->addMinute(5);
        //     $temp += rand(-3, 3) * 0.1;
        //     $hum += rand(-3, 3) * 0.1;
        //
        //     Monitoring::factory()->for($device)->create([
        //         'temperature' => $temp,
        //         'humidity' => $hum,
        //         'recorded_at' => $timestamp->addSeconds(rand(30, 70)),
        //     ]);
        // }
    }
}
