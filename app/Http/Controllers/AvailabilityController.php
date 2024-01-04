<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Availability;
use App\Models\AppointmentType;
use Carbon\Carbon;
use App\Http\Controllers\GoogleCalendarController;


class AvailabilityController extends Controller
{
    public function availability_by_user_id(Request $request, $user_id)
    {
        $availabilities = Availability::where('user_id', $user_id)->get();
        return response()->json($availabilities, 200);
    }


    public function set_user_availability(Request $request, $user_id)
    {
        $availability_exists = Availability::where('user_id', $user_id)->exists();
        if ($availability_exists) {
            $availability = Availability::where('user_id', $user_id)->first();
            $availability->availability = $request->input('availability');
            $availability->save();
            return response()->json(['message' => 'Availability Saved Successfully'], 200);
        }

        $availability = new Availability();
        $availability->user_id = $user_id;
        $availability->days = 5;
        $availability->availability = $request->input('availability');
        $availability->save();

        return response()->json(['message' => 'Availability Created Successfully'], 200);
    }


    /** 
     * Get Available Days 
     * @param int $user_id
     * @return Object[]
     */
    public function get_available_days(Request $request, $user_id)
    {
        $available_days = Availability::where('user_id', $user_id)->exists();
        if (!$available_days) {
            return response()->json(['message' => 'This user has not set any availabilities.'], 500);
        }
        $available_days = Availability::where('user_id', $user_id)->firstOrFail();
        return response()->json(json_decode($available_days)->availability, 200);
    }


    /** 
     * Get Available Times 
     * @param int|int|int dayIndex, userId, appointmentTypeId
     * @return Object[]
     */
    public function get_available_times(Request $request, $dayIndex, $user_id, $appointment_type_id, $date)
    {
        // dd('here');
        $appointment_type = AppointmentType::where('store_id', $request->user()->store_id)
            ->where('id', $appointment_type_id)
            ->firstOrFail();

        $appointment_type_interval = $appointment_type->length;

        $availability_exists = Availability::where('user_id', $user_id)->exists();
        if (!$availability_exists) {
            return response()->json(['message' => 'This user has not set any availabilities.'], 500);
        }
        $availability = Availability::where('user_id', $user_id)->firstOrFail();
        $available_times = $availability->availability[$dayIndex];

        if (!$available_times) {
            return response()->json(['message' => 'No availabilities set', 'times' => $available_times], 500);
        }
        $start = Carbon::parse($available_times[0]['start']);
        $end = Carbon::parse($available_times[0]['end']);

        $timeSlots = [];
        while ($start->addMinutes($appointment_type_interval)->lte($end)) {
            $timeSlots[] = [
                'start' => $start->subMinutes($appointment_type_interval)->format('h:i A'),
                'end' => $start->addMinutes($appointment_type_interval)->format('h:i A')
            ];

            $start->addMinutes($appointment_type->buffer_time);
        }

        // Remove conflicting time slots
        // $gcal = new GoogleCalendarController();
        // $gcal_events_encoded = $gcal->get_booked_slots($user_id)->original;
        // $check = [];
        // $times = [];
        // foreach ($gcal_events_encoded as $index => $event) {
        //     $gCaleventStart = Carbon::parse($event['start']);
        //     $gCaleventEnd = Carbon::parse($event['end']);
        //     if ($gCaleventStart->format('d-m-Y') === $date) {
        //         $gCalStartTime = Carbon::parse($event['start'])->setTimezone('Asia/Karachi')->format('h:i A');
        //         $gCalEndTime = Carbon::parse($event['end'])->setTimezone('Asia/Karachi')->format('h:i A');
        //         foreach ($timeSlots as $slot) {
        //             $slotStartTime = Carbon::parse($slot['start'])->format('h:i A');
        //             $slotEndTime = Carbon::parse($slot['end'])->format('h:i A');
        //             $check = [$slotStartTime, $slotEndTime];
        //             if (Carbon::parse($gCalEndTime)->between($slotStartTime, $slotEndTime)) {
        //                 $times[] = array_search($slot, $timeSlots);
        //                 unset($timeSlots[array_search($slot, $timeSlots)]);
        //             }
        //         }
        //     }
        // }
        $filteredTimeSlots = array_values($timeSlots);
        return response()->json($filteredTimeSlots, 200);
    }
}
