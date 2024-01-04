<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use Faker\Factory as Faker;

class NotificationsSeeder extends Seeder
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
            Notification::create([
                'type' => $faker->randomElement(['email', 'sms']),
                'read' => $faker->boolean,
                'for' => 1,
                'content' => $faker->sentence,
            ]);
        }
    }
}
