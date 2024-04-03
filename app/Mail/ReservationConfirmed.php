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
    public $slot;

    public function __construct($user, $slot)
    {
        $this->user = $user;
        $this->slot = $slot;
    }

    public function build()
    {
        return $this->subject('Confirmation de votre réservation')
            ->view('emails.reservationConfirmed');
    }
}
