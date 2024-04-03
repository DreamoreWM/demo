<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use hasFactory;
    protected $fillable = ['name','prenom','email'];

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function scopeSearch($query, $value)
    {
        $query->where('name','like',"%{$value}%")->orWhere('email','like',"%{$value}%");
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

}
