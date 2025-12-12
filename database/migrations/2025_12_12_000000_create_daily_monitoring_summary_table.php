<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitoring_summaries', function (Blueprint $table) {
            $table->id();
            // Timestamp yang menandai awal periode 12 jam (misalnya, 2025-12-12 00:00:00 atau 12:00:00)
            $table->timestamp('period_start')->unique(); 
            $table->decimal("avg_temperature", 5, 2);
            $table->decimal("avg_humidity", 5, 2);
            $table->foreignId("device_id")
                ->constrained('devices') // Merujuk ke tabel devices
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->unsignedInteger('data_count')->default(0); // Berapa banyak data raw yang diagregasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_summaries');
    }
};