<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Prestation;
use App\Models\Slot;
use App\Models\TemporaryUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $slots = Slot::with('appointment', 'employee')->get();
        $employees = Employee::all();
        $prestations = Prestation::all();

        $events = $slots->map(function ($slot) use ($employees) {
            $start_time_only = Carbon::parse($slot->start_time)->format('H:i:s');
            $end_time_only = Carbon::parse($slot->end_time)->format('H:i:s');

            $start = Carbon::parse($slot->date . ' ' . $start_time_only);
            $end = Carbon::parse($slot->date . ' ' . $end_time_only);


            $isSlotFree = is_null($slot->appointment);

            $employee = $employees->find($slot->employee_id);
            $employeeData = $employee ? [
                'id' => $employee->id,
                'name' => $employee->name,
                'color' => $employee->color,
            ] : null;

            return [
                'id' => $slot->id,
                'title' => $isSlotFree? "L" : "O",
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
                'color' => $isSlotFree ? 'green' : 'red',
                'employee' => $employeeData,
            ];
        });

        return view('calendar', [
            'events' => $events,
            'employees' => $employees,
            'prestations' => $prestations,
        ]);
    }

    public function assign(Request $request)
    {
        if ($request->has('user_id')) {
            // Décomposer la valeur user_id pour déterminer si c'est un utilisateur ou un utilisateur temporaire
            [$type, $id] = explode('-', $request->user_id);

            if ($type == 'user') {
                // Traitement pour un utilisateur régulier
                $user = User::find($id);
                $appointment = new Appointment();
                // Assurez-vous que votre modèle Appointment a la méthode bookable() configurée correctement pour la relation polymorphique
                $appointment->bookable()->associate($user);
            } elseif ($type == 'temporary') {
                // Traitement pour un utilisateur temporaire
                // Aucune action requise ici si vous traitez déjà le cas 'new' ci-dessous
            }
        } elseif ($request->user_id == 'new') {
            // Création d'un nouvel utilisateur temporaire si l'ID est 'new'
            $temporaryUser = new TemporaryUser();
            $temporaryUser->name = $request->user_name; // Assurez-vous que ces champs sont présents dans votre formulaire
            $temporaryUser->email = $request->user_email;
            $temporaryUser->save();

            $appointment = new Appointment();
            $appointment->bookable()->associate($temporaryUser);
        }

        if (isset($appointment)) {
            // Commun pour tous les cas
            $appointment->slot_id = $request->slot_id;
            $appointment->save();

            return redirect()->back()->withSuccess('Appointment created successfully.');
        }

        // Gérer le cas où aucune condition ci-dessus n'est satisfaite
        return redirect()->back()->withError('Unable to create appointment.');
    }
}
