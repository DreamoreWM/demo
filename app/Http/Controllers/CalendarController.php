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

        $events = $this->generateCalendarEvents($appointments, $employees, $openDays);

        return view('calendar', [
            'events' => $events,
            'employees' => $employees,
            'prestations' => $prestations,
        ]);
    }

    private function generateCalendarEvents($appointments, $employees, $openDays)
    {
        $events = [];
        $appointmentSlots = [];

        // Récupérer les créneaux des rendez-vous
        foreach ($appointments as $appointment) {
            $start = Carbon::parse($appointment->start_time);
            $end = Carbon::parse($appointment->end_time);

            $appointmentSlots[] = [
                'start' => $start->format('Y-m-d H:i'),
                'end' => $end->format('Y-m-d H:i'),
                'employee_id' => $appointment->employee_id,
            ];
        }

        // Générer les événements du calendrier
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
                                $slotStart = $startDate->format('Y-m-d') . ' ' . date('H:i', $currentTime);
                                $slotEnd = $startDate->format('Y-m-d') . ' ' . date('H:i', $currentTime + 3600);

                                $isSlotReserved = false;
                                foreach ($appointmentSlots as $appointmentSlot) {
                                    if ($this->doSlotsOverlap($slotStart, $slotEnd, $appointmentSlot['start'], $appointmentSlot['end']) && $appointmentSlot['employee_id'] == $employee->id) {
                                        $isSlotReserved = true;
                                        break;
                                    }
                                }

                                $events[] = [
                                    'title' => $isSlotReserved ? 'Reserved' : 'Available',
                                    'start' => $slotStart,
                                    'end' => $slotEnd,
                                    'color' => $isSlotReserved ? 'red' : 'green',
                                    'employee' => [
                                        'id' => $employee->id,
                                        'name' => $employee->name,
                                        'color' => $employee->color,
                                    ],
                                ];
                            }
                        }
                        $currentTime += 3600;
                    }
                }
            }

            $startDate->addDay(); // Passer au jour suivant
        }

        return $events;
    }

    private function doSlotsOverlap($slot1Start, $slot1End, $slot2Start, $slot2End)
    {
        return (
            (Carbon::parse($slot1Start)->lte(Carbon::parse($slot2End))) &&
            (Carbon::parse($slot1End)->gte(Carbon::parse($slot2Start)))
        );
    }

    public function assign(Request $request)
    {
        // Le reste du code reste inchangé
    }
}
