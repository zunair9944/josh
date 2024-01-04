<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::with('insuranceProvider')
        ->where('store_id', Auth::user()->store_id)
        ->get();

        $patientsWithInsuranceProviders = $patients->map(function ($patient) {
            $patientData = $patient->toArray();
            $patientData['insurance_provider_name'] = $patient->insuranceProvider->name;
            unset($patientData['insurance_provider']); // Remove the entire insurance_provider object
            return $patientData;
        });
        
        return response()->json($patientsWithInsuranceProviders, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $store_id = Auth::user()->store_id; 

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'insurance_provider' => 'required|exists:insurance_providers,id',
            'insurance_policy_number' => 'required|string|max:255',
            'insurance_group_number' => 'required|string|max:255',
            'prescription_values' => 'nullable|json'
        ]);

        try {
            $patient = Patient::create([
                'store_id' => $store_id,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'address_1' => $request->input('address_1'),
                'address_2' => $request->input('address_2'),
                'state' => $request->input('state'),
                'zip' => $request->input('zip'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'phone' => $request->input('phone'),
                'insurance_group_number' => $request->input('insurance_group_number'),
                'insurance_policy_number' => $request->input('insurance_policy_number'),
                'insurance_provider' => $request->input('insurance_provider'),
                'last_appointment' => $request->input('last_appointment'),
                'next_appointment' => $request->input('next_appointment'),
                'prescription_values' => json_encode($request->input('prescription')),
            ]);

            return response()->json(['message' => 'Patient Created Successfully','patient' => $patient]);
        } catch (\Throwable $th) {
           return response()->json(['message' => 'Patient coud not be created', 'error' => $th]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $storeId = Auth::user()->store_id;
        $patient = Patient::with('insuranceProvider')
            ->where('store_id', $storeId)
            ->findOrFail($id);
        
        $patientData = $patient->toArray();
        $patientData['insurance_provider_name'] = $patient->insuranceProvider->name;
        $patientData['insurance_provider'] = $patient->insuranceProvider->id;

        return response()->json($patientData);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'email',
            'address_1' => 'string|max:255',
            'address_2' => 'string|max:255',
            'state' => 'string|max:255',
            'zip' => 'string|max:255',
            'country' => 'string|max:255',
            'insurance_provider' => 'integer',
            'insurance_policy_number' => 'string|max:255',
            'insurance_group_number' => 'string|max:255',
            'last_appointment' => 'date',
            'next_appointment' => 'date',
            'prescription_values' => 'json',
        ]);
    
        $patient = Patient::findOrFail($id);
        $patient->update($validatedData);
    
        return response()->json($patient, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::findOrFail($id);
        if ($patient->store_id !== Auth::user()->store_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $patient->delete();
        return response()->json(['message' => 'Patient deleted successfully'], 200);
    }



    public function get_upcoming_bookings(Request $request, $id)
    {
        $patientId = $id;
        $now = Carbon::now();

        $bookings = Booking::with(['appointmentType', 'user'])
            ->where('patient_id', $patientId)
            ->where('end_time', '>', $now)
            ->get();

        $bookingsWithDetails = $bookings->map(function ($booking) use ($now) {
            $endTime = Carbon::parse($booking->end_time);
            $timeRemaining = $now->diffAsCarbonInterval($endTime);

            $bookingData = $booking->toArray();
            $bookingData['time_remaining'] = $timeRemaining->format('%h hours %i minutes');
            $bookingData['appointment_type_name'] = $booking->appointmentType->title;
            $bookingData['user_first_name'] = $booking->user->first_name;
            $bookingData['user_last_name'] = $booking->user->last_name;

            unset($bookingData['user']);
            unset($bookingData['appointment_type']);

            return $bookingData;
        });

        return response()->json($bookingsWithDetails);
    }

}
