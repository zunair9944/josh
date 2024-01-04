<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $table = 'availabilities';
    protected $fillable = ['days', 'availability', 'user_id'];

    protected $casts = [
        'availability' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
