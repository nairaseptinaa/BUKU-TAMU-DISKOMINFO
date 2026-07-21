<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $serviceTypes = [
            'Konsultasi',
            'Pengaduan',
            'Permohonan Surat',
            'Kunjungan Kerja',
        ];

        foreach ($serviceTypes as $name) {
            DB::table('service_types')->updateOrInsert(
                ['service_name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}