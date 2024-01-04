<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AppointmentType;
use Illuminate\Support\Str;
use App\Models\Questionnaire;
use App\Models\User;

class AppointmentTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointmentTypes = AppointmentType::where('store_id', Auth::user()->store_id)->get();

        $userIds = [];
        $questionnaireIds = [];
        foreach ($appointmentTypes as $appointmentType) {
            $userIds = array_merge($userIds, $appointmentType->users);
            $questionnaireIds[] = $appointmentType->questionnaire_id;
        }

        $users = User::whereIn('id', $userIds)->pluck('first_name', 'id');
        $questionnaires = Questionnaire::whereIn('id', $questionnaireIds)->pluck('title', 'id');

        $result = [];
        foreach ($appointmentTypes as $appointmentType) {
            $userNames = [];
            foreach ($appointmentType->users as $userId) {
                if (isset($users[$userId])) {
                    $userNames[] = $users[$userId];
                }
            }

            $questionnaireTitle = isset($questionnaires[$appointmentType->questionnaire_id]) ? $questionnaires[$appointmentType->questionnaire_id] : '';

            $result[] = [
                'appointmentType' => $appointmentType,
                'userNames' => $userNames,
                'questionnaireTitle' => $questionnaireTitle,
            ];
        }

        return response()->json($result);
    }


    public function total_store_appointment_types(Request $request)
    {
        $total_count = AppointmentType::where('store_id', Auth::user()->store_id)->count();
        return response()->json($total_count, 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'length' => 'required|integer',
            'icon' => 'string',
            'limit' => 'required|integer',
            'buffer_time' => 'required|integer',
            'users' => 'required|array',
            'questionnaire_id' => 'required|exists:questionnaires,id',
        ]);

        $validatedData['store_id'] = Auth::user()->store_id;
        $validatedData['slug'] = Str::slug($validatedData['title']);
    
        $appointmentType = AppointmentType::create($validatedData);
    
        return response()->json($appointmentType, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointmentType = AppointmentType::findOrFail($id);
        if ($appointmentType->store_id != Auth::user()->store_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json($appointmentType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|max:255',
            'length' => 'integer',
            'icon' => 'string',
            'limit' => 'integer',
            'beforeEventBuffer' => 'integer',
            'afterEventBuffer' => 'integer',
            'users' => 'array',
            'questionnaire_id' => 'exists:questionnaires,id',
        ]);
        
        $validatedData['store_id'] = Auth::user()->store_id;
        
        $appointmentType = AppointmentType::findOrFail($id);
        $appointmentType->update($validatedData);
        
        return response()->json($appointmentType, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointmentType = AppointmentType::findOrFail($id);
        if ($appointmentType->store_id != Auth::user()->store_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $appointmentType->delete();
        return response()->json(['message' => 'Appointment type deleted successfully']);
    }


    /** 
     * Get Appointment Types By User Ids
     */
    public function get_appointment_types_by_user(Request $request,$id)
    {
        $appointment_types = AppointmentType::where('store_id', $request->user()->store_id)->get();
        $applicable_appoinmtent_types = [];
        if($appointment_types){
            foreach($appointment_types as $app_type){
                if(in_array($id,$app_type['users'])){
                    $applicable_appoinmtent_types[] = $app_type;
                }
            }
        }
        return response()->json($applicable_appoinmtent_types,200);
    }
}
