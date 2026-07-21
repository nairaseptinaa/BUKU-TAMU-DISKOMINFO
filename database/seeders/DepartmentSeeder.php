<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'BAPEDDA',
            'Dinas Dalduk KB Dan PPPA',
            'Dinas Kesehatan, P2KB',
            'Dinas Pendidikan Dan Kebudayaan',
            'Dinas Kependudukan Dan Pencatatan Sipil',
            'Sekertariat Daerah (SETDA)',
        ];

        foreach ($departments as $name) {
            DB::table('departments')->updateOrInsert(
                ['department_name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}