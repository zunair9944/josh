<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $table = 'questionnaires';
    protected $fillable = ['title', 'questions'];


    protected $casts = [
        'questions' => 'json',
    ];
}
