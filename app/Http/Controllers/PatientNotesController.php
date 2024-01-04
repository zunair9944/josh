<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\PatientNote;
use App\Http\Controllers\AttachmentsController;

class PatientNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $storeID = Auth::user()->store_id;
        $patientNotes = PatientNote::whereHas('patient', function ($query) use ($storeID) {
            $query->where('store_id', $storeID);
        })->get();

        return response()->json($patientNotes);
    }


    public function get_patient_notes(Request $request, $id)
    {
        $storeID = Auth::user()->store_id;
        $patientNotes = PatientNote::whereHas('patient', function ($query) use ($storeID) {
            $query->where('store_id', $storeID);
        })->get();
        return response()->json($patientNotes,200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $patient_id)
    {
        $storeID = Auth::user()->store_id;
        $patient = Patient::where('store_id', $storeID)->findOrFail($patient_id);
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'record' => 'nullable|file'
        ]);


        $attachment_id = null;
        if(isset($validatedData['record'])){
            $attachmentController = new AttachmentsController();
            $attachment_id = $attachmentController->store($patient_id,$validatedData['record']);
        }

        // Create the patient note
        $patientNote = new PatientNote();
        $patientNote->title = $validatedData['title'];
        $patientNote->content = $validatedData['content'];
        $patientNote->attachment_id = $attachment_id;
        $patientNote->patient_id = $patient_id;
        $patientNote->save();

        return response()->json($patientNote, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient_note = PatientNote::findOrFail($id);
        return response()->json($patient_note,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $storeID = Auth::user()->store_id;
        $patientNote = PatientNote::where('id', $id)->whereHas('patient', function ($query) use ($storeID) {
            $query->where('store_id', $storeID);
        })->first();

        if (!$patientNote) {
            return response()->json(['message' => 'Patient note not found or not accessible.'], 404);
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'record' => 'nullable|file'
        ]);

        $patientNote->title = $validatedData['title'];
        $patientNote->content = $validatedData['content'];

        if (isset($validatedData['record'])) {
            $attachmentController = new AttachmentsController();
            $attachment_id = $attachmentController->store($patientNote->patient_id, $validatedData['record']);
            $patientNote->attachment_id = $attachment_id;
        }

        $patientNote->save();

        return response()->json($patientNote, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
