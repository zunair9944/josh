<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        
        $this->call(UserSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(AppointmentTypesSeeder::class);
        $this->call(BookingsSeeder::class);
        $this->call(InsuranceProviderSeeder::class);
        $this->call(NotificationsSeeder::class);
        $this->call(PatientsSeeder::class);
        // $this->call(RolesSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(NoticesSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
