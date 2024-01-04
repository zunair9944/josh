<?php

namespace Database\Seeders;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the users table with dummy data
        $user1 = User::create([
            'username' => 'user1',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'user1@example.com',
            'emailVerified' => now(),
            'password' => Hash::make('password123'), // Hash your password
            'role' => 'store_owner', // Assign a role
            'store_id' => 1, // Replace with the corresponding store ID
            // Add other columns as needed
        ]);

        $user2 = User::create([
            'username' => 'user2',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'user2@example.com',
            'emailVerified' => now(),
            'password' => Hash::make('password456'), // Hash your password
            'role' => 'store_manager', // Assign a role
            'store_id' => 1, // Replace with the corresponding store ID
            // Add other columns as needed
        ]);
    }

}
