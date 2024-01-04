<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;


    protected $table = 'bookings';
    protected $fillable = ['user_id', 'patient_id', 'appointment_type_id', 'rescheduled', 'cancellation_reason',
        'status', 'start_time', 'end_time', 'reserved_frame_id', 'intake_questionnaire_answers',
        'notification_id'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'intake_questionnaire_answers' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class);
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
