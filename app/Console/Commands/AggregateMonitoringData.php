<?php

namespace App\Console\Commands;

use App\Models\Monitoring;
use App\Models\MonitoringSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AggregateMonitoringData extends Command
{
    protected $signature = 'monitoring:aggregate';
    protected $description = 'Aggregates raw monitoring data into 12-hour summaries.';

    public function handle()
    {
        // 1. Tentukan batas waktu terakhir yang sudah diagregasi (misalnya 12 jam lalu)
        $endPeriod = Carbon::now()->subHours(12)->endOfHour(); 
        // Kita hanya akan memproses data yang direkam SEBELUM 12 jam yang lalu

        $rawLogs = Monitoring::where('recorded_at', '<', $endPeriod)
            ->groupBy('device_id')
            ->selectRaw('device_id, AVG(temperature) as avg_temperature, AVG(humidity) as avg_humidity, MIN(recorded_at) as period_start, COUNT(id) as data_count')
            ->get();

        if ($rawLogs->isEmpty()) {
            $this->info("No new raw data to aggregate.");
            return;
        }

        foreach ($rawLogs as $log) {
            // Karena kita mengagregasi data 12 jam yang lalu, period_start adalah waktu 12 jam yang lalu
            $period = $log->period_start->startOfHour(); // Ambil waktu awal dan bulatkan ke jam

            MonitoringSummary::updateOrCreate(
                [
                    'device_id' => $log->device_id,
                    'period_start' => $period,
                ],
                [
                    'avg_temperature' => round($log->avg_temperature, 2),
                    'avg_humidity' => round($log->avg_humidity, 2),
                    'data_count' => $log->data_count,
                ]
            );
        }

        $this->info("Successfully aggregated " . $rawLogs->count() . " device summaries.");
    }
}