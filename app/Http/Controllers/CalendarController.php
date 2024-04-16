<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Prestation;
use App\Models\SalonSetting;
use App\Models\TemporaryUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $appointments = Appointment::all();
        $employees = Employee::all();
        $prestations = Prestation::all();
        $setting = SalonSetting::first();
        $openDays = json_decode($setting->open_days, true);

        $events = $this->generateCalendarEvents($appointments, $employees);
        $availableSlots = $this->getAvailableSlots($openDays, $employees);
        $availableEvents = $this->generateAvailableEvents($availableSlots, $employees);

        return view('calendar', [
            'events' => array_merge($events, $availableEvents),
            'employees' => $employees,
            'prestations' => $prestations,
        ]);
    }

    private function generateCalendarEvents($appointments, $employees)
    {
        $events = [];

        foreach ($appointments as $appointment) {
            $start = Carbon::parse($appointment->start_time);
            $end = Carbon::parse($appointment->end_time);

            $employee = $employees->find($appointment->employee_id);
            $employeeData = $employee ? [
                'id' => $employee->id,
                'name' => $employee->name,
                'color' => $employee->color,
            ] : null;

            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->prestations->pluck('nom')->implode(', '),
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
                'color' => 'red',
                'employee' => $employeeData,
            ];
        }

        return $events;
    }

    private function generateAvailableEvents($availableSlots, $employees)
    {
        $events = [];

        foreach ($availableSlots as $slot) {
            $start = Carbon::createFromFormat('Y-m-d H:i', $slot['date'] . ' ' . $slot['start']);
            $end = Carbon::createFromFormat('Y-m-d H:i', $slot['date'] . ' ' . $slot['end']);

            $employee = $employees->find($slot['employee_id']);
            $employeeData = $employee ? [
                'id' => $employee->id,
                'name' => $employee->name,
                'color' => $employee->color,
            ] : null;

            $events[] = [
                'title' => 'Available',
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
                'color' => 'green',
                'employee' => $employeeData,
            ];
        }

        return $events;
    }

    private function getAvailableSlots($openDays, $employees)
    {
        $availableSlots = [];

        $startDate = now();
        $endDate = now()->addMonth();

        while ($startDate <= $endDate) {
            $dayOfWeek = $startDate->format('w'); // 0 (dimanche) à 6 (samedi)

            foreach ($employees as $employee) {
                $employeeSchedule = $employee->schedules()->where('day_of_week', $dayOfWeek)->first();

                if ($employeeSchedule) {
                    $openHours = $openDays[strtolower($startDate->format('l'))];
                    $scheduleStart = strtotime($employeeSchedule->start_time);
                    $scheduleEnd = strtotime($employeeSchedule->end_time);
                    $scheduleBreakStart = strtotime($employeeSchedule->break_start);
                    $scheduleBreakEnd = strtotime($employeeSchedule->break_end);
                    $shopBreakStart = strtotime($openHours['break_start']);
                    $shopBreakEnd = strtotime($openHours['break_end']);

                    $currentTime = max($scheduleStart, strtotime($openHours['open']));
                    while ($currentTime + 3600 <= min($scheduleEnd, strtotime($openHours['close']))) {
                        // Vérifier si le créneau n'est pas pendant la pause de l'employé
                        if ($currentTime + 3600 <= $scheduleBreakStart || $currentTime >= $scheduleBreakEnd) {
                            // Vérifier si le créneau n'est pas pendant la pause du salon
                            if ($currentTime + 3600 <= $shopBreakStart || $currentTime >= $shopBreakEnd) {
                                $slotStart = date('H:i', $currentTime);
                                $slotEnd = date('H:i', $currentTime + 3600);
                                $availableSlots[] = [
                                    'start' => $slotStart,
                                    'end' => $slotEnd,
                                    'date' => $startDate->format('Y-m-d'),
                                    'employee_id' => $employee->id,
                                ];
                            }
                        }
                        $currentTime += 3600;
                    }
                }
            }

            $startDate->addDay(); // Passer au jour suivant
        }

        return $availableSlots;
    }

    public function assign(Request $request)
    {
        // Le reste du code reste inchangé
    }
}
