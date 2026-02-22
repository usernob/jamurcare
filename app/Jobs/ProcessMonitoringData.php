<?php

namespace App\Jobs;

use App\Events\DeviceMonitoringUpdate;
use App\Models\Device;
use App\Models\Monitoring;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMonitoringData implements ShouldQueue
{
    use Queueable;

    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function handle(): void
    {
        $device = Device::where("ulid", $this->payload["device_ulid"])->first();

        if (!$device){
            $device = Device::create(["ulid" => $this->payload["device_ulid"], "name" => "dummy_device"]);
        }

        $record = new Monitoring();

        $record["temperature"] = $this->payload["temperature"];
        $record["humidity"] = $this->payload["humidity"];
        $record["device_id"] = $device->id;
        $record["recorded_at"] = now();

        $record->save();

        DeviceMonitoringUpdate::dispatch($device->ulid, $record);
    }
}
