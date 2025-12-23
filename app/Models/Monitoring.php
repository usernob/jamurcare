<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Monitoring extends Model
{
    /** @use HasFactory<\Database\Factories\MonitoringFactory> */
    use HasFactory;

    protected $table = "monitoring";
    public $timestamps = false;
    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
