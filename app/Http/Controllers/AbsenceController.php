<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentMoved;
use App\Models\Absence;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AbsenceController extends Controller
{
    public function index()
    {
        $absences = Absence::with('employee')->get();
        return view('absences.index', compact('absences'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('absences.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $validatedData['start_time'] = \Carbon\Carbon::parse($request->start_time);
        $validatedData['end_time'] = \Carbon\Carbon::parse($request->end_time);

        $absence = Absence::create($validatedData);

        // Récupérer les rendez-vous de l'employé pendant l'absence
        $appointments = Appointment::where('employee_id', $request->employee_id)
            ->whereBetween('start_time', [$validatedData['start_time'], $validatedData['end_time']])
            ->get();

        foreach ($appointments as $appointment) {
            // Vérifier si un autre employé est disponible
            $otherEmployees = Employee::where('id', '!=', $request->employee_id)->get();
            $otherEmployeeAvailable = false;

            foreach ($otherEmployees as $otherEmployee) {
                // Vérifier si l'employé a un horaire disponible
                if ($this->isSlotAvailable($appointment->start_time, $appointment->end_time, $otherEmployee->id) && $this->hasSchedule($otherEmployee->id, $appointment->start_time, $appointment->end_time)) {
                    // Déplacer le rendez-vous à l'autre employé
                    $appointment->employee_id = $otherEmployee->id;
                    $appointment->save();

                    // Envoyer un e-mail au client pour l'informer du changement
                    $client = $appointment->bookable;
                    \Mail::to($client->email)->send(new AppointmentMoved($client, $appointment));

                    $otherEmployeeAvailable = true;
                    break;
                }
            }

            if (!$otherEmployeeAvailable) {
                // Annuler le rendez-vous et envoyer un e-mail au client
                $client = $appointment->bookable;
                \Mail::to($client->email)->send(new AppointmentCancelled($client, $appointment));

                $appointment->delete();
            }
        }

        return redirect()->route('absences.index');
    }

    private function isSlotAvailable($startTime, $endTime, $employeeId)
    {
        // Convertir les heures de début et de fin en instances Carbon
        $startTime = \Carbon\Carbon::parse($startTime);
        $endTime = \Carbon\Carbon::parse($endTime);

        // Récupérer les rendez-vous de l'employé pendant ce créneau
        $appointments = Appointment::where('employee_id', $employeeId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->get();

        // Récupérer les absences de l'employé pendant ce créneau
        $absences = Absence::where('employee_id', $employeeId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->get();

        // Si l'employé a un rendez-vous ou une absence pendant ce créneau, le créneau n'est pas disponible
        if ($appointments->count() > 0 || $absences->count() > 0) {
            return false;
        }

        // Vérifier si le créneau est dans l'horaire de l'employé
        if (!$this->hasSchedule($employeeId, $startTime, $endTime)) {
            return false;
        }

        // Sinon, le créneau est disponible
        return true;
    }
    public function edit(Absence $absence)
    {
        $employees = Employee::all();
        return view('absences.edit', compact('absence', 'employees'));
    }

    public function update(Request $request, Absence $absence)
    {
        $absence->update($request->all());
        return redirect()->route('absences.index');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        return redirect()->route('absences.index');
    }

    private function hasSchedule($employeeId, $startTime, $endTime)
    {
        dump($startTime, $endTime, $employeeId);
        // Convertir les heures de début et de fin en instances Carbon
        $startTime = \Carbon\Carbon::parse($startTime);
        $endTime = \Carbon\Carbon::parse($endTime);

        // Récupérer l'horaire de l'employé pour le jour du rendez-vous
        $schedule = EmployeeSchedule::where('employee_id', $employeeId)
            ->where('day_of_week', $startTime->dayOfWeekIso)
            ->first();

        // Si l'employé n'a pas d'horaire pour ce jour, retourner false
        if (!$schedule) {
            return false;
        }

        // Convertir les heures de début et de fin de l'horaire en instances Carbon
        $scheduleStartTime = \Carbon\Carbon::createFromTimeString($schedule->start_time)->setDate($startTime->year, $startTime->month, $startTime->day);
        $scheduleEndTime = \Carbon\Carbon::createFromTimeString($schedule->end_time)->setDate($endTime->year, $endTime->month, $endTime->day);

        // Si l'horaire de l'employé ne couvre pas le rendez-vous, retourner false
        if ($startTime->lt($scheduleStartTime) || $endTime->gt($scheduleEndTime)) {
            return false;
        }

        // Sinon, retourner true
        return true;
    }}
