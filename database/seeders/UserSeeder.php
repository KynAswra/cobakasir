<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin Mama Ilham',
            'email'    => 'admin@mamailham.com',
            'password' => Hash::make('123456'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Kasir Budi Santoso',
            'email'    => 'kasir@mamailham.com',
            'password' => Hash::make('123456'),
            'role'     => 'kasir',
        ]);
    }
}
