<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryUser extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'temporary_users';

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    // Ici, vous pouvez ajouter d'autres méthodes de modèle ou des relations si nécessaire

    // Dans User.php et TemporaryUser.php
    public function appointments()
    {
        return $this->morphMany(Appointment::class, 'bookable');
    }

}
