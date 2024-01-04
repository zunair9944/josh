<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InsuranceProvider; 

class InsuranceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insuranceProviders = [
            'Manulife',
            'Blue Cross',
            'Group Source',
            'Johnston Group',
            'Great West Life',
            'Canada Life',
            'Claim Secure',
            'Desjardin',
            'Johnson Insurance',
            'La Capitale',
            'Cowan',
            'Manion',
            'Group Health',
            'Sun Life Financial',
            'Telus Health',
            'CINUP',
            'Green Shield Canada',
        ];

        foreach ($insuranceProviders as $insuranceProvider) {
            InsuranceProvider::firstOrCreate(['name' => $insuranceProvider]);
        }
    }
}
