<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestation extends Model
{
    use HasFactory;


    // Protéger les champs assignables en masse
    protected $fillable = [
        'nom', 'description', 'prix', 'temps',
    ];

    /**
     * Relation avec le modèle Slot (si vous avez un modèle Slot pour les créneaux horaires).
     * Cela suppose que vous avez un modèle Slot qui a une clé étrangère 'prestation_id'.
     */
    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_prestation');
    }
}
