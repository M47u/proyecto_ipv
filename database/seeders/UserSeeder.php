<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrador
        User::create([
            'name' => 'Administrador IPV',
            'email' => 'admin@ipv.gob.ar',
            'password' => Hash::make('Admin123!'),
            'role' => 'administrador',
            'is_active' => true,
            'phone' => '3794000000',
        ]);

        // Inspector 1
        User::create([
            'name' => 'Juan Carlos Pérez',
            'email' => 'inspector1@ipv.gob.ar',
            'password' => Hash::make('Inspector123!'),
            'role' => 'inspector',
            'is_active' => true,
            'phone' => '3794111111',
        ]);

        // Inspector 2
        User::create([
            'name' => 'María Laura González',
            'email' => 'inspector2@ipv.gob.ar',
            'password' => Hash::make('Inspector123!'),
            'role' => 'inspector',
            'is_active' => true,
            'phone' => '3794222222',
        ]);
    }
}
