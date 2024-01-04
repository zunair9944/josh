<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\User;
use App\Models\Patient;
use App\Models\AppointmentType;
use App\Models\Notification;
use Carbon\Carbon;
use App\Http\Controllers\GoogleCalendarController;


class BookingsController extends Controller
{



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('bookings')->get();
        $formattedData = $users->map(function ($user) {
            return [
                'data' => $user
            ];
        });

        return response()->json($formattedData);
    }


    public function get_store_bookings(Request $request)
    {
        $store_id = $request->user()->store_id;
        
        $bookings = Booking::with(['appointmentType', 'patient', 'user'])
            ->where('store_id', $store_id)
            ->get();

        $bookingsWithDetails = $bookings->map(function ($booking) {
            $bookingData = $booking->toArray();
            $bookingData['appointment_type_name'] = $booking->appointmentType->title;
            $bookingData['start_time'] = Carbon::parse($bookingData['start_time'])->toDateTimeString();
            $bookingData['end_time'] = Carbon::parse($bookingData['end_time'])->toDateTimeString();
            $bookingData['patient_name'] = $booking->patient->first_name . ' ' . $booking->patient->last_name;
            $bookingData['for'] = $booking->user->first_name . ' ' . $booking->user->last_name;

            unset($bookingData['user']);
            unset($bookingData['appointment_type']);
            unset($bookingData['patient']);

            return $bookingData;
        });

        return response()->json($bookingsWithDetails);

    }


    public function total_store_bookings_count(Request $request)
    {
        // dd('here');
        $total_count = Booking::where('store_id', $request->user()->store_id)->count();
        return response()->json($total_count,200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'patient_id' => 'required|integer',
            'appointment_type_id' => 'required|integer',
            'status' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $user = User::find($validatedData['user_id']);
        $patient = Patient::find($validatedData['patient_id']);
        $appointment_type = AppointmentType::find($validatedData['appointment_type_id']);

        $booking = new Booking();
        $booking->store_id = $request->user()->store_id;
        $booking->user_id = $validatedData['user_id'];
        $booking->patient_id = $validatedData['patient_id'];
        $booking->appointment_type_id = $validatedData['appointment_type_id'];
        $booking->status = $validatedData['status'];
        $booking->start_time = $validatedData['start_time'];
        $booking->end_time = $validatedData['end_time'];
        $booking->reserved_frame_id = (isset($validatedData['reserved_frame_id'])) ? $validatedData['reserved_frame_id'] : 0;
        $booking->intake_questionnaire_answers = (isset($validatedData['intake_questionnaire_answers'])) ? $validatedData['intake_questionnaire_answers'] : [];

        /** Create Google Calendar Event */
        $event_details = [
            'event_name' => $appointment_type->title  . ' Appointment with ' . $user->first_name . ' ' . $user->last_name,
            'start' => $validatedData['start_time'],
            'end' => $validatedData['end_time']
        ];

        // $gcal = new GoogleCalendarController();
        // $gcal_event = $gcal->create_event($validatedData['user_id'],$event_details);

        /** Create Outlook Event */


        /** Create iCal Event */


        /** Create Notification */
        // need to pass array of ids so store_owner gets their notification about booking too.
        $notification = new Notification();
        $notification->type = 'New Booking';
        $notification->for = $validatedData['user_id'];
        $notification->read = false;
        $notification->content = $appointment_type->title  . ' Appointment with ' . $user->first_name . ' ' . $user->last_name;
        $notification->save();



        // $booking->gcal_event_id = $gcal_event->id;
        $booking->ol_event_id = 0;
        $booking->ical_event_id = 0;
        $booking->notification_id = $notification->id;
        $booking->save();

        /** Now Finally Send out the Emails */
        
        
        return response()->json($booking, 201);
    }


     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $booking = Booking::with('user','patient','appointmentType')->findOrFail($id);

        // Format time as hours:minutes AM/PM
        $startTime = \Carbon\Carbon::parse($booking->start_time)->format('h:i A');
        $endTime = \Carbon\Carbon::parse($booking->end_time)->format('h:i A');

        $date = \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y');

        $booking->start_time_formatted = $startTime;
        $booking->end_time_formatted = $endTime;
        $booking->date_formatted = $date;

        return response()->json($booking);
    }


     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $validatedData = $request->validate([
        ]);

        $booking->update($validatedData);
        return response()->json($booking);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function get_booking_by_date(Request $request, $date)
    {
        $formattedDate = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        $bookings = Booking::whereDate('start_time', $formattedDate)->get();
    
        return $bookings;
    }



    /**
     * Get Bookings By User
     * Gets events in integrated calendars and the platform. 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_bookings_by_user(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)->get();
        $google_calendar_controller = new GoogleCalendarController();
        $gcal_events = $google_calendar_controller->get_booked_slots($request);

        $combinedTimes = [];
        if ($gcal_events && isset($gcal_events->original)) {
            foreach ($gcal_events->original as $event) {
                if (isset($event['event_start']) && isset($event['event_end'])) {
                    $startDateTime = Carbon::parse($event['event_start']);
                    $endDateTime = Carbon::parse($event['event_end']);
                    $startTime = $startDateTime->format('H:i');
                    $endTime = $endDateTime->format('H:i');
                    $combinedTimes[] = [
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ];
                }
            }
        }

        return response()->json($combinedTimes, 200);



    }



}
