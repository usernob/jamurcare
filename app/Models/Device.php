<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceFactory> */
    use HasFactory, HasUlids;

    public $fillable = [
        "ulid",
        "name"
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function monitoring(): HasMany {
        return $this->hasMany(Monitoring::class)->chaperone();
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
