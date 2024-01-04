<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendar_id',
        'is_primary',
        'primary_sub_calendar',
        'check_for_conflicts'
    ];

    public function connectedCalendar()
    {
        return $this->belongsTo(ConnectedCalendar::class, 'calendar_id');
    }
}
