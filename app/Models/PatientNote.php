<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientNote extends Model
{
    use HasFactory;

    protected $table = 'patient_notes';
    protected $fillable = ['title', 'content', 'patient_id'];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class, 'attachment_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
