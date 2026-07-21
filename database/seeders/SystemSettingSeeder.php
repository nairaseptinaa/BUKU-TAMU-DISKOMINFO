<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'skm_redirect_url'],
            [
                'setting_value' => 'https://skm.go.id/share/instansi/1caa90bc-ce8c-4288-b725-8bc07beb55d3/1',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}