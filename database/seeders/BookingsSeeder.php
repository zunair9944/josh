<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use Faker\Factory as Faker;

class BookingsSeeder extends Seeder
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
            Booking::create([
                'store_id' => 1,
                'user_id' => $faker->numberBetween(1,2),
                'patient_id' => $faker->numberBetween(3, 4),
                'appointment_type_id' => $faker->numberBetween(1, 5),
                'rescheduled' => $faker->boolean,
                'cancellation_reason' => $faker->sentence,
                'status' => $faker->randomElement(['pending', 'confirmed', 'cancelled']),
                'start_time' => $faker->dateTimeBetween('+1 week', '+2 weeks'),
                'end_time' => $faker->dateTimeBetween('+2 weeks', '+3 weeks'),
                'reserved_frame_id' => $faker->numberBetween(1, 10),
                'intake_questionnaire_answers' => json_encode([]),
                'gcal_event_id' => $faker->uuid,
                'ol_event_id' => $faker->uuid,
                'ical_event_id' => $faker->uuid,
                'notification_id' => $faker->numberBetween(3, 7),
            ]);
        }
    }
}
