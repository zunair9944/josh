<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RescheduleBooking extends Model
{
    use HasFactory;


    protected $fillable = [
        'booking_id',
        'rescheduled_to',
    ];

    protected $casts = [
        'rescheduled_to' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}


