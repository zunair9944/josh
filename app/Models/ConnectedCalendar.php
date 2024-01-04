<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectedCalendar extends Model
{
    use HasFactory;
    protected $table = 'connected_calendars';
    protected $fillable = ['user_id','slug', 'access_token','refresh_token'];



    public function userCalendars()
    {
        return $this->hasMany(UserCalendar::class, 'calendar_id');
    }
}
