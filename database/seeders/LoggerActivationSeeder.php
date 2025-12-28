<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoggerActivation;
use Illuminate\Support\Str;

class LoggerActivationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Spesifik (Biar kelihatan rapi buat demo)
        LoggerActivation::create([
            'logger_id' => 'LOG-JKT-001',
            'logger_name' => 'Main Stack Logger - Jakarta',
            'token' => Str::random(40), // Token panjang acak
            'activation_code' => 'TRUSUR-01',
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        LoggerActivation::create([
            'logger_id' => 'LOG-SBY-002',
            'logger_name' => 'Boiler Sensor - Surabaya',
            'token' => Str::random(40),
            'activation_code' => 'TRUSUR-02',
            'status' => 'Inactive', // Contoh yang mati
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Data Random Tambahan (Biar tabel kelihatan penuh)
        for ($i = 3; $i <= 10; $i++) {
            LoggerActivation::create([
                'logger_id' => 'LOG-GEN-' . str_pad($i, 3, '0', STR_PAD_LEFT), // Hasil: LOG-GEN-003
                'logger_name' => 'Sensor Unit ' . $i,
                'token' => Str::random(40),
                'activation_code' => strtoupper(Str::random(8)),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}