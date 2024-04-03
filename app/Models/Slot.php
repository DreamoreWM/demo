<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

        protected $fillable = ['employee_id', 'start_time', 'end_time', 'day_of_week', 'date'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];


    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function appointment()
    {
        return $this->hasOne(Appointment::class);
    }

    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }
}
