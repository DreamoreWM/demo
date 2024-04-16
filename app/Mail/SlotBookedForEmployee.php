<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Prestation;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SlotBookedForEmployee extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $user;
    public $prestations;

    public function __construct($user, $appointment, $prestations)
    {
        $this->appointment = $appointment;
        $this->user = $user;
        $this->prestations = $prestations;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Slot Booked For Employee',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.slotBookedForEmployee', // Assurez-vous que le chemin de la vue est correct
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
