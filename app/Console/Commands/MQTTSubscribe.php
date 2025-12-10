<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMonitoringData;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\MqttClient;

class MQTTSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start process to subscribe to MQTT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mqtt = MQTT::connection();

        $mqtt->subscribe("jamur/+/monitoring", function (string $topic, string $message) {
            printf("Received QoS level 1 message on topic [%s]: %s\n", $topic, $message);
            $payload = json_decode($message, true);
            ProcessMonitoringData::dispatch($payload);
        }, MqttClient::QOS_AT_LEAST_ONCE);

        $mqtt->loop(true);
    }
}
