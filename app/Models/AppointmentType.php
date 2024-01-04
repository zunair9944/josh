<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentType extends Model
{
    use HasFactory;

    protected $table = 'appointment_types';
    protected $fillable = ['title', 'slug', 'length','store_id','icon', 'limit', 'buffer_time', 'users',
        'questionnaire_id'];

    protected $casts = [
        'users' => 'json',
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
