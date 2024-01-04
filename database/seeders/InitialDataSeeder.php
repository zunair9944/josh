<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $initialData = [
            // Your provided data here
        ];

        foreach ($initialData as $data) {
            // Insert user data
            $user = User::create($data['settings']);

            // Insert notifications
            foreach ($data['notifications'] as $notificationData) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $notificationData['title'],
                    'read' => $notificationData['read'],
                ]);
            }

            // Insert bookings
            foreach ($data['bookings'] as $bookingData) {
                $booking = Booking::create([
                    'user_id' => $user->id,
                    // Insert other booking data
                ]);

                // Insert intake questions for the booking
                $booking->intakeQuestions()->createMany($bookingData['intake_questions']);
            }

            // Insert patients
            foreach ($data['patients'] as $patientData) {
                $patient = Patient::create([
                    'user_id' => $user->id,
                    // Insert other patient data
                ]);

                // Insert notes for the patient
                $patient->notes()->createMany($patientData['notes']);
            }

            // Insert appointment types
            foreach ($data['appointment_types'] as $appointmentTypeData) {
                AppointmentType::create($appointmentTypeData);
            }

            // Insert availability
            foreach ($data['availability'] as $availabilityData) {
                Availability::create([
                    'user_id' => $user->id,
                    // Insert other availability data
                ]);
            }

            // Insert questionnaires
            foreach ($data['questionnaires'] as $questionnaireData) {
                $questionnaire = Questionnaire::create([
                    'user_id' => $user->id,
                    // Insert other questionnaire data
                ]);

                // Insert questionnaire questions
                foreach ($questionnaireData['questions'] as $questionData) {
                    $questionnaire->questions()->create($questionData);
                }
            }
        }
    }
}
