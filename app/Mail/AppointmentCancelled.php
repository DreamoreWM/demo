<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $appointment;

    public function __construct($client, $appointment)
    {
        $this->client = $client;
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this->markdown('emails.cancelled');
    }
}
