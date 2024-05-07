<?php

namespace App\Http\Controllers;

use App\Models\Absence;
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
    public $slotDurationInMinutes;
    public $slotDurationInSec;
    public function index()
    {
        $appointments = Appointment::all();
        $employees = Employee::all();
        $prestations = Prestation::all();
        $setting = SalonSetting::first();
        $slotDurationInMinutes = SalonSetting::first()->slot_duration;
        $slotDuration = gmdate("H:i", $slotDurationInMinutes * $slotDurationInMinutes);
        $slotDurationInSec = $slotDurationInMinutes * 60;
        $this->slotDurationInSec = $slotDurationInSec;
        $this->slotDurationInMinutes = $slotDurationInMinutes;
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
            'slotDuration' => $slotDuration,
            'slotDurationInMinutes' => $slotDurationInMinutes,
            'slotDurationInSeconds' => $slotDurationInSec,
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
                'appointment_id' => $appointment->id,
            ];
        }

        // Générer les événements du calendrier
        $startDate = now();
        $endDate = now()->addMonth();

        while ($startDate <= $endDate) {
            $dayOfWeek = $startDate->format('w'); // 0 (dimanche) à 6 (samedi)

            foreach ($employees as $employee) {
                $employeeSchedule = $employee->schedules()->where('day_of_week', $dayOfWeek)->first();

                $absences = Absence::where('employee_id', $employee->id)->get();

                if ($employeeSchedule) {
                    $openHours = $openDays[strtolower($startDate->format('l'))];
                    $scheduleStart = strtotime($employeeSchedule->start_time);
                    $scheduleEnd = strtotime($employeeSchedule->end_time);
                    $scheduleBreakStart = strtotime($employeeSchedule->break_start);
                    $scheduleBreakEnd = strtotime($employeeSchedule->break_end);
                    $shopBreakStart = strtotime($openHours['break_start']);
                    $shopBreakEnd = strtotime($openHours['break_end']);

                    $currentTime = max($scheduleStart, strtotime($openHours['open']));
                    while ($currentTime + $this->slotDurationInSec <= min($scheduleEnd, strtotime($openHours['close']))) {
                        // Vérifier si le créneau n'est pas pendant la pause de l'employé
                        $isDuringAbsence = false;
                        foreach ($absences as $absence) {
                            $absenceStart = Carbon::parse($absence->start_time)->format('Y-m-d H:i');
                            $absenceEnd = Carbon::parse($absence->end_time)->format('Y-m-d H:i');
                            $slotStart = $startDate->format('Y-m-d') . ' ' . date('H:i', $currentTime);
                            $slotEnd = $startDate->format('Y-m-d') . ' ' . date('H:i', $currentTime + $this->slotDurationInSec );

                            if ($this->doSlotsOverlap($slotStart, $slotEnd, $absenceStart, $absenceEnd)) {
                                $isDuringAbsence = true;
                                break;
                            }
                        }

                        if ($isDuringAbsence) {
                            $currentTime += $this->slotDurationInSec ;
                            continue;
                        }

                        if ($currentTime + $this->slotDurationInSec  <= $scheduleBreakStart || $currentTime >= $scheduleBreakEnd) {
                            // Vérifier si le créneau n'est pas pendant la pause du salon
                            if ($currentTime + $this->slotDurationInSec  <= $shopBreakStart || $currentTime >= $shopBreakEnd) {
                                $slotStart = $startDate->format('Y-m-d') . ' ' . date('H:i', $currentTime);
                                $slotEnd = $startDate->format('Y-m-d') . ' ' . date('H:i', $currentTime + $this->slotDurationInSec );

                                $isSlotReserved = false;
                                foreach ($appointmentSlots as $appointmentSlot) {
                                    if ($this->doSlotsOverlap($slotStart, $slotEnd, $appointmentSlot['start'], $appointmentSlot['end']) && $appointmentSlot['employee_id'] == $employee->id) {
                                        $isSlotReserved = true;
                                        $appointment = $appointments->firstWhere('id', $appointmentSlot['appointment_id']);
                                        break;
                                    }
                                }

                                $events[] = [
                                    'id' => $appointment->id ?? null,
                                    'title' => $employee->name,
                                    'start' => $slotStart,
                                    'end' => $slotEnd,
                                    'start_time' => $isSlotReserved ? Carbon::parse($appointment->start_time)->format('H:i:s') : null,
                                    'end_time' => $isSlotReserved ? Carbon::parse($appointment->end_time)->format('H:i:s') : null,
                                    'reserved' => $isSlotReserved,
                                    'color' => $isSlotReserved ? 'red' : 'green',
                                    'employee' => [
                                        'id' => $employee->id,
                                        'name' => $employee->name,
                                        'color' => $employee->color,
                                    ],
                                    // Ajouter les informations supplémentaires ici
                                    'client' => [
                                        'name' => $appointment->bookable->name ?? null,
                                        'email' => $appointment->bookable->email ?? null,
                                    ],
                                    'prestations' => $appointment ?? null ? $appointment->prestations->map(function ($prestation) {
                                        return [
                                            'id' => $prestation->id ?? null,
                                            'name' => $prestation->nom ?? null,
                                            'duration' => $prestation->temps ?? null,
                                        ];
                                    }) : null,
                                ];
                            }
                        }
                        $currentTime += $this->slotDurationInSec ;
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

    public function delete(Request $request)
    {
        // Récupérer l'ID du rendez-vous à supprimer
        $appointmentId = $request->input('id');

        // Récupérer le rendez-vous de la base de données
        $appointment = Appointment::find($appointmentId);

        if ($appointment) {
            // Supprimer le rendez-vous
            $appointment->delete();

            // Renvoyer une réponse JSON indiquant que la suppression a réussi
            return response()->json(['success' => true]);
        } else {
            // Renvoyer une réponse JSON indiquant que la suppression a échoué
            return response()->json(['success' => false, 'error' => 'Appointment not found']);
        }
    }
}
