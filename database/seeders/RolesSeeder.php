<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'store_owner']);
        Role::firstOrCreate(['name' => 'store_manager']);
        Role::firstOrCreate(['name' => 'staff']);
    }

    
}
