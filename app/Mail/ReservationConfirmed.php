<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $appointment;

    public function __construct($user, $appointment)
    {
        $this->user = $user;
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this->subject('Confirmation de votre réservation')
            ->view('emails.reservationConfirmed');
    }
}
