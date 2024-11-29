<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'postcode',
        'appointment_date',
        'client_name',
        'client_email',
        'client_phone',
        'employee_id',
        'location_lat',
        'location_lng',
        'distance',
        'duration',
        'departure_time',  
        'available_time',
        'address',
        
    ];
}
