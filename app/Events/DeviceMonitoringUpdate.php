<?php

namespace App\Events;

use App\Models\Monitoring;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceMonitoringUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ulid;
    public $model;

    public function __construct(string $ulid, Monitoring $data)
    {
        $this->ulid = $ulid;
        $this->model = $data;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('device.' . $this->ulid);
    }

    public function broadcastWith(): array
    {
        $unixtime = $this->model->recorded_at->valueOf();
        return [
            "temperature" => ["x" => $unixtime, "y" => floatval($this->model->temperature)],
            "humidity" => ["x" => $unixtime, "y" => floatval($this->model->humidity)]
        ];
    }
}
