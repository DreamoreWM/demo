<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_token',
        'google_refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function refreshTokenIfNeeded($client)
    {

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($this->google_refresh_token);
            $newAccessToken = $client->getAccessToken();
            $this->update([
                'google_token' => $newAccessToken['access_token'],
            ]);
        }
    }

    public function scopeSearch($query, $value)
    {
        $query->where('name','like',"%{$value}%")->orWhere('email','like',"%{$value}%");
    }

    public function getAppointments()
    {
        return $this->appointments()->with('slot')->get();
    }

    // Dans User.php et TemporaryUser.php
    public function appointments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Appointment::class, 'bookable');
    }

    public function isAdmin()
    {
        return $this->role == 'admin'; // ou toute autre logique que vous utilisez pour déterminer si un utilisateur est un administrateur
    }



}
