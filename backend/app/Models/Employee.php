<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
    ];

    /**
     * Çalışanın kullanıcı ilişkisi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Çalışanın randevuları (appointments) ilişkisi.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
