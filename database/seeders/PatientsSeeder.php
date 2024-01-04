<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Faker\Factory as Faker;

class PatientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Patient::create([
                'store_id' => 1,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'address_1' => $faker->streetAddress,
                'address_2' => $faker->secondaryAddress,
                'state' => $faker->state,
                'phone' => '+923216582791',
                'zip' => $faker->postcode,
                'country' => $faker->country,
                'insurance_provider' => 1,
                'insurance_policy_number' => $faker->ean8,
                'insurance_group_number' => $faker->ean13,
                'last_appointment' => $faker->dateTimeBetween('-1 year', 'now'),
                'next_appointment' => $faker->dateTimeBetween('now', '+1 year'),
                'prescription_values' => ['value1' => $faker->randomLetter, 'value2' => $faker->randomLetter],
            ]);
        }
    }
}
