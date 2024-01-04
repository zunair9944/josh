<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendBookingNotifications extends Command
{

    protected $signature = 'app:send-booking-notifications';
    protected $description = 'Check bookings and perform actions based on start time';

    public function handle()
    {
        $currentTime = Carbon::now();

        // Find bookings within 25 hours
        $bookings25Hours = Booking::where('start_time', '>', $currentTime)
            ->where('start_time', '<=', $currentTime->addHours(25))
            ->get();

        foreach ($bookings25Hours as $booking) {
            // Mail::to($booking->user->email)->send(new BookingReminder());
            $this->info('Booking within 25 hours: ' . $booking->id);
        }



        // Reset the current time
        $currentTime = Carbon::now();

        // Find bookings within 1 hour
        $bookings1Hour = Booking::where('start_time', '>', $currentTime)
            ->where('start_time', '<=', $currentTime->addHour())
            ->get();

        foreach ($bookings1Hour as $booking) {
            // Mail::to($booking->user->email)->send(new BookingReminder());
            $this->info('Booking within 1 hour: ' . $booking->id);
        }


        // Reset the current time
        $currentTime = Carbon::now();

        // Find bookings within 13 hours
        $bookings13Hours = Booking::where('start_time', '>', $currentTime)
            ->where('start_time', '<=', $currentTime->addHours(13))
            ->get();


        foreach ($bookings13Hours as $booking) {
            // Mail::to($booking->user->email)->send(new BookingReminder());
            $this->info('Booking within 13 hours: ' . $booking->id);
        }
    }
}
