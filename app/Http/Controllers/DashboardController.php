<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Monitoring;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(User $user, string $ulid): View
    {
        $devices = Device::all();

        $current = $devices->firstWhere('ulid', $ulid);
        $others  = $devices->where('ulid', '!=', $ulid);
        return view('dashboard.index', [
            "current_device" => $current,
            "other_devices" => $others
        ]);
    }

    public function getMonitoringData(string $ulid)
    {
        $logs = Monitoring::whereHas('device', function ($q) use ($ulid) {
            $q->where('ulid', $ulid);
        })
            ->orderBy('recorded_at', 'desc')
            ->take(100)
            ->get();

        $data = [
            "temperature" => [],
            "humidity" => [],
        ];

        foreach ($logs as $log) {
            $unixtime = strtotime($log["recorded_at"]) * 1000;
            array_push($data["temperature"], ["x" => $unixtime, "y" => floatval($log["temperature"])]);
            array_push($data["humidity"], ["x" => $unixtime, "y" => floatval($log["humidity"])]);
        }
        return $data;
    }
}
