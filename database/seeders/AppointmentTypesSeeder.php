<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppointmentType;
use Faker\Factory as Faker;

class AppointmentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            AppointmentType::create([
                'title' => $faker->word,
                'slug' => $faker->slug,
                'icon' => 'general-eye-exam',
                'length' => $faker->numberBetween(15, 60),
                'limit' => $faker->numberBetween(1, 10),
                'beforeEventBuffer' => $faker->numberBetween(5, 15),
                'afterEventBuffer' => $faker->numberBetween(5, 15),
                'users' => [],
                'store_id' => 1,
                'questionnaire_id' => null,
            ]);
        }
    }
}
