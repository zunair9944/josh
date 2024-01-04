<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceProvider extends Model
{
    use HasFactory;
    protected $table = 'insurance_providers';
    protected $fillable = ['name'];


    public function patients(){
        return $this->hasMany(Patient::class);
    }
}
