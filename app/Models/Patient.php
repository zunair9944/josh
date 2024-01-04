<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $fillable = ['store_id','first_name', 'last_name', 'email','phone', 'address_1', 'address_2', 'state', 'zip', 'country',
        'insurance_provider', 'insurance_policy_number', 'insurance_group_number', 'prescription_values'];

    protected $casts = [
        'last_appointment' => 'datetime',
        'next_appointment' => 'datetime',
        'prescription_values' => 'json',
    ];

    public function insuranceProvider()
    {
        return $this->belongsTo(InsuranceProvider::class, 'insurance_provider');
    }
}
