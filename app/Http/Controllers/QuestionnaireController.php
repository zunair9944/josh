<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questionnaire;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $store_id = $request->user()->store_id; 
        $questionnaires = Questionnaire::where('store_id', $store_id)->get();
        return response()->json($questionnaires,200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $store_id = $request->user()->store_id; 
        $validatedData = $request->validate([
            'title' => 'required|string',
            'questions' => 'required',
        ]);

        $questionnaire = new Questionnaire();
        $questionnaire->store_id = $store_id; 
        $questionnaire->title = $validatedData['title'];
        $questionnaire->questions = json_encode($validatedData['questions']);

        $questionnaire->save();
        return response()->json(['Questionnaire Created Successfully'],200);
    }

//     public function store(Request $request)
// {
//     $store_id = $request->user()->store_id;

//     // Check if the user has the 'store_owner' or 'store_manager' role
//     if ($request->user()->hasAnyRole(['store_owner', 'store_manager'])) {
//         $validatedData = $request->validate([
//             'title' => 'required|string',
//             'questions' => 'required',
//         ]);

//         $questionnaire = new Questionnaire();
//         $questionnaire->store_id = $store_id;
//         $questionnaire->title = $validatedData['title'];
//         $questionnaire->questions = json_encode($validatedData['questions']);

//         $questionnaire->save();
//         return response()->json(['Questionnaire Created Successfully'], 200);
//     } else {
//         return response()->json(['You do not have permission to add questionnaires.'], 403);
//     }
// }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $questionnaire = Questionnaire::where('id', $id)
        ->where('store_id', $request->user()->store_id)
        ->first();
        if (!$questionnaire) {
            return response()->json(['message' => 'No Questionnaire with this ID exists.'],200);
        }
        return response()->json($questionnaire,200);
    }
    

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'questions' => 'nullable',
        ]);
        
        $questionnaire = Questionnaire::findOrFail($id);
        $questionnaire->title = $validatedData['title'];
        $questionnaire->questions = empty($validatedData['questions']) ? '[]' : json_encode($validatedData['questions']);
        
        $questionnaire->save();
        
        return response()->json(['Questionnaire Updated Successfully'], 200);
    }


    public function destroy(string $id)
    {
        try {
            $questionnaire = Questionnaire::findOrFail($id);
            $questionnaire->delete();
            return response()->json(['message' => 'Questionnaire deleted successfully']);
        } catch (\Illuminate\Database\QueryException $exception) {
            if ($exception->errorInfo[1] === 1451) {
                return response()->json(['message' => 'Failed to delete the questionnaire. It is associated with an Appointment Type.'], 400);
            }
            return response()->json(['message' => 'Failed to delete the questionnaire.'], 400);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to delete the questionnaire.'], 400);
        }
    }



}
