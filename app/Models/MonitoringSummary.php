// app/Models/MonitoringSummary.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringSummary extends Model
{
    use HasFactory;

    protected $table = 'monitoring_summaries';

    protected $fillable = [
        'device_id',
        'period_start',
        'avg_temperature',
        'avg_humidity',
        'data_count',
    ];

    protected $casts = [
        'period_start' => 'datetime',
    ];

    // Relasi ke Device (Opsional, tapi bagus)
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}