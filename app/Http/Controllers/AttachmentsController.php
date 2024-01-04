<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attachment;

class AttachmentsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store($patient_id,$file)
    {
        $filename = $file->getClientOriginalName();
        $path = $file->storeAs('attachments/' . $file, $filename);

        $attachment = new Attachment();
        $attachment->src = $path;
        $attachment->save();
        
        return $attachment->id;
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
