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

        $users = User::all();
        $temporaryUsers = TemporaryUser::all();

        $events = $this->generateCalendarEvents($appointments, $employees, $openDays);

        return view('calendar', [
            'events' => $events,
            'employees' => $employees,
            'prestations' => $prestations,
            'users' => $users,
            'temporaryUsers' => $temporaryUsers,
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
                                    'reserved' => $isSlotReserved,
                                    'color' => $isSlotReserved ? 'red' : 'green',
                                    'employee' => [
                                        'id' => $employee->id,
                                        'name' => $employee->name,
                                        'color' => $employee->color,
                                    ],
                                    // Ajouter les informations supplémentaires ici
                                    'client' => [
                                        'name' => $appointment->bookable->name,
                                        'email' => $appointment->bookable->email,
                                    ],
                                    'prestations' => $appointment->prestations->map(function ($prestation) {
                                        return [
                                            'id' => $prestation->id,
                                            'name' => $prestation->nom,
                                            'duration' => $prestation->temps,
                                        ];
                                    }),
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
        $user_id = $request->input('user_id');



        $employee_id = $request->input('employeeId');
        $selectedPrestationsInfos = json_decode($request->input('selectedPrestationsInfos'), true);
        $eventStart = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('eventStart'));
        $eventEnd = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('eventStart'));
        $totalDuration = $request->input('totalDuration');

        if (str_contains($user_id, '-')) {
            // Décomposer la valeur user_id pour déterminer si c'est un utilisateur ou un utilisateur temporaire

            [$type, $id] = explode('-', $user_id);

            if ($type == 'user') {
                // Traitement pour un utilisateur régulier
                $user = User::find($id);

                $appointment = Appointment::create([
                    'employee_id' => $employee_id,
                    'start_time' => $eventStart->addMinutes(120),
                    'end_time' => $eventEnd->addMinutes($totalDuration + 120),
                    'bookable_id' => $user->id,
                    'bookable_type' => get_class($user),
                ]);
            } elseif ($type == 'temporary') {

                $temporaryUser = TemporaryUser::find($id);

                $appointment = Appointment::create([
                    'employee_id' => $employee_id,
                    'start_time' => $eventStart->addMinutes(120),
                    'end_time' => $eventEnd->addMinutes($totalDuration + 120),
                    'bookable_id' => $temporaryUser->id,
                    'bookable_type' => get_class($temporaryUser),
                ]);

                $user = $temporaryUser;
            }
        } elseif ($request->user_id == 'new') {
            // Création d'un nouvel utilisateur temporaire si l'ID est 'new'
            $temporaryUser = new TemporaryUser();
            $temporaryUser->name = $request->user_name; // Assurez-vous que ces champs sont présents dans votre formulaire
            $temporaryUser->email = $request->user_email;
            $temporaryUser->save();

            $appointment = Appointment::create([
                'employee_id' => $employee_id,
                'start_time' => $eventStart->addMinutes(120),
                'end_time' => $eventEnd->addMinutes($totalDuration + 120),
                'bookable_id' => $temporaryUser->id,
                'bookable_type' => get_class($temporaryUser),
            ]);

            $user = $temporaryUser;
        }


            foreach ($selectedPrestationsInfos as $prestation) {
                $appointment->prestations()->attach($prestation['id']);
            }

            $employee = Employee::where('id',$employee_id)->first();

            // Envoyer les e-mails de confirmation
            $prestations = $appointment->prestations()->get();
            \Mail::to($user->email)->send(new \App\Mail\ReservationConfirmed($user, $appointment, $prestations));
            \Mail::to($employee->email)->send(new \App\Mail\SlotBookedForEmployee($user, $appointment, $prestations));

            // Ajouter l'événement au calendrier Google
            $this->addEventToGoogleCalendar($user, $appointment);

            return redirect('/calendar')->with('success', 'Le créneau a été réservé avec succès.');

    }

    private function isSlotAvailable($startTime, $endTime, $employeeId)
    {
        $requestedSlot = [
            'start' => Carbon::parse($startTime),
            'end' => Carbon::parse($endTime),
        ];

        $existingAppointments = Appointment::where('employee_id', $employeeId)->get();

        foreach ($existingAppointments as $appointment) {
            $existingSlot = [
                'start' => Carbon::parse($appointment->start_time),
                'end' => Carbon::parse($appointment->end_time),
            ];

            if ($this->doSlotsOverlap($requestedSlot, $existingSlot)) {
                return false;
            }
        }

        return true;
    }

    private function getSelectedPrestations(Request $request)
    {
        $selectedPrestations = [];
        $prestationIds = $request->input('prestation_ids', []);

        foreach ($prestationIds as $prestationId) {
            $selectedPrestations[] = $prestationId;
        }

        return $selectedPrestations;
    }

    private function addEventToGoogleCalendar($user, $appointment)
    {
        // Logique pour ajouter l'événement au calendrier Google
        // (similaire à la méthode dans ReservationComponent)
    }
}
