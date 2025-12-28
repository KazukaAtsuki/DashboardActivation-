<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'admins@trusur.com',
            'role' => 'Administrator',
            'password' => Hash::make('password123'), // Passwordnya ini
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}