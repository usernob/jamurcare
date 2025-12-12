<?php

namespace App\Console\Commands;

use App\Models\Monitoring; // Pastikan Model Monitoring di-import
use Illuminate\Console\Command;
use Illuminate\Support\Carbon; // Pastikan Carbon di-import

class CleanOldMonitoringData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:cleanup'; // Nama command yang lebih singkat dan umum

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes raw monitoring logs older than 7 days.'; // Deskripsi baru

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Tentukan batas waktu: 7 hari yang lalu
        $sevenDaysAgo = Carbon::now()->subDays(7); 

        // 2. Lakukan penghapusan data
        // Menghapus data dari tabel 'monitoring' yang 'recorded_at'-nya lebih tua dari 7 hari yang lalu
        $deletedCount = Monitoring::where('recorded_at', '<', $sevenDaysAgo)->delete();

        // 3. Output hasil ke konsol
        $this->info("Successfully deleted {$deletedCount} old raw monitoring records (older than 7 days).");
    }
}