<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['appointment_id', 'rating', 'comment', 'photo_id'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function photo()
    {
        return $this->hasMany(Photo::class);
    }
}
