<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['mahasiswa', 'reviewer', 'panitia', 'admin', 'management'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@ukt.ac.id'],
            ['name' => 'Admin UKT', 'password' => Hash::make('password')]
        );
        $admin->assignRole('admin');

        // Reviewer
        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@ukt.ac.id'],
            ['name' => 'Reviewer UKT', 'password' => Hash::make('password')]
        );
        $reviewer->assignRole('reviewer');

        // Panitia
        $panitia = User::firstOrCreate(
            ['email' => 'panitia@ukt.ac.id'],
            ['name' => 'Panitia UKT', 'password' => Hash::make('password')]
        );
        $panitia->assignRole('panitia');

        // Management
        $management = User::firstOrCreate(
            ['email' => 'management@ukt.ac.id'],
            ['name' => 'Management UKT', 'password' => Hash::make('password')]
        );
        $management->assignRole('management');

        // Mahasiswa demo
        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@ukt.ac.id'],
            ['name' => 'Mahasiswa Demo', 'password' => Hash::make('password')]
        );
        $mahasiswa->assignRole('mahasiswa');
    }
}
