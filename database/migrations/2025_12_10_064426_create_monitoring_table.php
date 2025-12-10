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
        Schema::create('monitoring', function (Blueprint $table) {
            $table->id();
            $table->timestamp("recorded_at")->useCurrent();
            $table->decimal("temperature", 5, 2);
            $table->decimal("humidity", 5, 2);
            $table->foreignId("device_id")
                ->constrained()
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->index(["device_id", "recorded_at"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring');
    }
};
