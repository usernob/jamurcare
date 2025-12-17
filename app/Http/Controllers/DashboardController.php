<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Monitoring;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;

class DashboardController extends Controller
{
    public function index(string $ulid, Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $user->load('devices');

        $currentDevice = $user->devices
            ->firstWhere('ulid', $ulid);

        abort_if(!$currentDevice, 404);

        return view('dashboard.index', [
            'user' => $user,
            'current_device' => $currentDevice,
        ]);
    }

    public function getMonitoringData(string $ulid)
    {
        $logs = Monitoring::whereHas('device', function ($q) use ($ulid) {
            $q->where('ulid', $ulid);
        })
            ->orderBy('recorded_at', 'desc')
            ->take(720)
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

    public function pingDevice(string $ulid)
    {
        MQTT::publish("jamur/$ulid/status_request", json_encode(["message" => "ping"]));
    }


    public function controlDevice(string $ulid, Request $request)
    {
        MQTT::publish("jamur/$ulid/control", json_encode($request->all()));
        return json_encode($request->all());
    }
}
