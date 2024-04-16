<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'start_time', 'end_time', 'bookable_id', 'bookable_type'];

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    // Assurez-vous que la table users existe et que vous avez un modèle User correspondant.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookable()
    {
        return $this->morphTo();
    }

    public function prestations()
    {
        return $this->belongsToMany(Prestation::class, 'appointment_prestation');
    }

}
