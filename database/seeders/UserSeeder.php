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
            'name' => 'Admin One',
            'username' => 'admin1', // Changed from email to username
            'password' => Hash::make('password'),
            'role' => 'admin',
            'division_id' => 1,
        ]);

        User::create([
            'name' => 'Admin Two',
            'username' => 'admin2', // Changed from email to username
            'password' => Hash::make('password'),
            'role' => 'admin',
            'division_id' => 2,
        ]);

        // Users
        User::create([
            'name' => 'User One',
            'username' => 'user1', // Changed from email to username
            'password' => Hash::make('password'),
            'role' => 'user',
            'division_id' => 3,
        ]);

        User::create([
            'name' => 'User Two',
            'username' => 'user2', // Changed from email to username
            'password' => Hash::make('password'),
            'role' => 'user',
            'division_id' => 4,
        ]);

        User::create([
            'name' => 'User Three',
            'username' => 'user3', // Changed from email to username
            'password' => Hash::make('password'),
            'role' => 'user',
            'division_id' => 5,
        ]);
    }
}