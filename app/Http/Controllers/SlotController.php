<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SlotController extends Controller
{
    public function index(Employee $employee)
    {
        return view('employees.slots.manage', compact('employee'));
    }

//    public function update(Request $request, Employee $employee)
//    {
//        $request->validate([
//            'day_of_week' => 'required|string',
//            'start_time' => 'required|date_format:H:i',
//            'end_time' => 'required|date_format:H:i|after:start_time',
//        ]);
//
//        $dayOfWeek = $request->day_of_week;
//        $startTime = $request->start_time;
//        $endTime = $request->end_time;
//
//        // Calcul de la date de début et de fin pour le mois à partir d'aujourd'hui
//        $startDate = now();
//        $endDate = now()->addMonth();
//
//        // Boucle sur les jours jusqu'à la fin du mois
//        while ($startDate->lte($endDate)) {
//            if ($startDate->isoFormat('dddd') === $dayOfWeek) {
//                // Vérification de l'existence de créneaux pour ce jour
//                $existingSlot = $employee->slots()->where('day_of_week', $dayOfWeek)->whereDate('date', $startDate->toDateString())->first();
//
//                if (!$existingSlot) {
//                    $employee->slots()->create([
//                        'day_of_week' => $dayOfWeek,
//                        'date' => $startDate->toDateString(),
//                        'start_time' => $startTime,
//                        'end_time' => $endTime,
//                    ]);
//                }
//            }
//            $startDate->addDay();
//        }
//
//        return redirect('/dashboard')->with('success', 'Les créneaux ont été enregistrés avec succès.');
//    }
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $dayOfWeek = $request->day_of_week;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // La date de début est aujourd'hui
        $startDate = Carbon::now();
        // La date de fin est un mois après aujourd'hui
        $endDate = Carbon::now()->addMonth();

        // Trouve le premier jour correspondant après ou sur aujourd'hui
        while ($startDate->dayOfWeekIso !== (int)$dayOfWeek) {
            $startDate->addDay();
        }

        // Itère jusqu'à un mois après aujourd'hui
        while ($startDate->lte($endDate)) {
            $existingSlot = $employee->slots()->where('date', $startDate->toDateString())->first();

            if (!$existingSlot) {
                $employee->slots()->create([
                    'employee_id' => $employee->id,
                    'day_of_week' => $dayOfWeek,
                    'date' => $startDate->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);
            }

            // Passe au même jour de la semaine suivante
            $startDate->addWeek();
        }

        return back()->with('success', 'Les créneaux ont été enregistrés avec succès pour un mois à partir d\'aujourd\'hui.');
    }








}
