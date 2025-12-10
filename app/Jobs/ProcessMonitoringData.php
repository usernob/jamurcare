<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Monitoring;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMonitoringData implements ShouldQueue
{
    use Queueable;

    public $payload;
    /**
     * Create a new job instance.
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $device = Device::where("ulid", $this->payload["device_uid"])->first();

        if (!$device){
            $device = Device::create(["ulid" => $this->payload["device_uid"], "name" => "dummy_device"]);
        }

        $record = new Monitoring();

        $record["recorded_at"] = $this->payload["recorded_at"];
        $record["temperature"] = $this->payload["temperature"];
        $record["humidity"] = $this->payload["humidity"];
        $record["device_id"] = $device->id;

        $record->save();
    }
}
