<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Slot;

class AppointmentController extends Controller
{
    /**
     * Affiche le formulaire pour créer un nouveau rendez-vous.
     */
    public function create()
    {
        // Récupère tous les coiffeurs pour les lister dans le formulaire
        $employees = Employee::all();

        return view('appointments.create', compact('employees'));
    }

    /**
     * Stocke un nouveau rendez-vous dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'slot_id' => 'required|exists:slots,id',
            // Ajoutez ici d'autres champs nécessaires
        ]);

        $appointment = new Appointment();
        $appointment->employee_id = $request->employee_id;
        $appointment->slot_id = $request->slot_id;
        // Assurez-vous de définir ici tous les autres champs requis
        $appointment->save();

        return redirect()->route('appointments.index')->with('success', 'Rendez-vous pris avec succès.');
    }

}
