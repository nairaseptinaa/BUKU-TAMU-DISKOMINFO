<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // AKUN ADMIN
        User::firstOrCreate(
            ['email' => 'admin@diskominfo.go.id'],
            [
                'name' => 'Admin Diskominfo',
                'password' => Hash::make('Admin123'),
            ]
        );

        $this->call([
            DepartmentSeeder::class,
            ServiceTypeSeeder::class,
            SystemSettingSeeder::class,
        ]);
    }
}