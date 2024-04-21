<?php

namespace App\Console\Commands;

use App\Mail\ReviewInvitation;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckAppointments extends Command
{
    protected $signature = 'check:appointments';

    protected $description = 'Check if appointments are completed and send review invitations';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $appointments = Appointment::all();

        foreach ($appointments as $appointment) {
            $endTime = \Carbon\Carbon::parse($appointment->end_time);
            if ($endTime->isPast() && !$appointment->review_invitation_sent) {
                Mail::to($appointment->bookable->email)->send(new ReviewInvitation($appointment));
                $appointment->review_invitation_sent = true;
                $appointment->save();
            }
        }
    }
}
