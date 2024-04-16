<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\EmployeeSchedule;
use App\Models\SalonSetting;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use DateTimeZone;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Livewire\Component;
use App\Models\Prestation;
use App\Models\Employee;

class ReservationComponent extends Component
{
    public $prestations;
    public $employees;
    public $openDays;
    public $selectedPrestations = [];
    public $selectedEmployee;
    public $availableSlots = [];
    public $showAddPrestationDiv = true;

    public $showResumePrestationDiv = true;

    public $selectedSlot = null;

    public function confirmReservation($date, $start)
    {
        // Récupérer les prestations sélectionnées
        $selectedPrestations = $this->getSelectedPrestations();

        // Calculer l'heure de fin en additionnant les durées de toutes les prestations
        $totalDuration = 0;
        foreach ($selectedPrestations as $prestation) {
            $totalDuration += $prestation['temps'];
        }

        // Convertir l'heure de début en objet DateTime
        $startDatetime = new DateTime($date . ' ' . $start);

        // Calculer l'heure de fin en ajoutant la durée totale des prestations à l'heure de début
        $endDatetime = clone $startDatetime;
        $endDatetime->add(new DateInterval('PT' . $totalDuration . 'M'));

        // Vérifier si le créneau est disponible
        if ($this->isSlotAvailable($startDatetime, $endDatetime)) {
            // Mettre à jour le tableau $selectedSlot avec l'heure de fin
            $this->selectedSlot = [
                'date' => $date,
                'start' => $start,
                'end' => $endDatetime->format('H:i') // Formater l'heure de fin dans le format HH:MM
            ];

            $userId = auth()->id(); // Assurez-vous que l'utilisateur est authentifié.
            $user = User::find($userId);

            // Créer la nouvelle réservation
            $appointment = Appointment::create([
                'employee_id' => $this->selectedEmployee,
                'start_time' => $this->selectedSlot['date'] . ' ' . $this->selectedSlot['start'],
                'end_time' => $this->selectedSlot['date'] . ' ' . $this->selectedSlot['end'],
                'bookable_id' => $user->id,
                'bookable_type' => get_class($user),
            ]);

            // Lier les prestations à la réservation
            foreach ($this->selectedPrestations as $prestationId) {
                $appointment->prestations()->attach($prestationId);
            }



            $prestations = $appointment->prestations()->get();

            \Mail::to($user->email)->send(new \App\Mail\ReservationConfirmed($user, $appointment, $prestations));

            $employee = Employee::where('id',$this->selectedEmployee)->first();

            \Mail::to($employee->email)->send(new \App\Mail\SlotBookedForEmployee($user, $appointment, $prestations));

            $this->addEventToGoogleCalendar($user, $appointment);

            // Réinitialiser les données
            $this->selectedPrestations = [];
            $this->selectedEmployee = null;
            $this->selectedSlot = null;
            $this->availableSlots = [];

            return redirect('/dashboard')->with('success', 'Le créneau a été réservé avec succès.');
        }
    }

    private function addEventToGoogleCalendar($user, $appointment)
    {
        $client = new Google_Client();
        // Configurez le client Google avec vos clés API
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setAccessType('offline');

        if ($user->google_refresh_token) {
            $client->refreshToken($user->google_refresh_token);
            $accessToken = $client->getAccessToken();
            $client->setAccessToken($accessToken);

            $user->google_token = $accessToken['access_token']; // Assurez-vous d'utiliser la clé correcte pour le jeton d'accès
            // Si disponible, vous pouvez aussi stocker la date d'expiration du jeton et le jeton de rafraîchissement actualisé
            if (isset($newAccessToken['expires_in'])) {
                $user->google_token_expires_at = now()->addSeconds($newAccessToken['expires_in']);
            }
            if (isset($newAccessToken['refresh_token'])) {
                $user->google_refresh_token = $newAccessToken['refresh_token'];
            }

            $user->save();
        }

        $service = new Google_Service_Calendar($client);

        // Convertir les heures de début et de fin en objets DateTime avec le bon fuseau horaire
        $startDateTime = new DateTime($appointment->start_time);
        $startDateTime->setTimezone(new DateTimeZone($user->timezone ?? 'UTC')); // Utiliser le fuseau horaire de l'utilisateur ou 'UTC' par défaut
        $startDateTime->modify('-2 hours'); // Ajouter 2 heures pour compenser le décalage

        $endDateTime = new DateTime($appointment->end_time);
        $endDateTime->setTimezone(new DateTimeZone($user->timezone ?? 'UTC'));
        $endDateTime->modify('-2 hours');

        $startEventDateTime = new Google_Service_Calendar_EventDateTime();
        $startEventDateTime->setDateTime($startDateTime->format('c'));

        $endEventDateTime = new Google_Service_Calendar_EventDateTime();
        $endEventDateTime->setDateTime($endDateTime->format('c'));

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Rendez-vous Coiffeur',
            'start' => ['dateTime' => $startEventDateTime->getDateTime()],
            'end' => ['dateTime' => $endEventDateTime->getDateTime()],
        ]);

        $calendarId = 'primary';
        $service->events->insert($calendarId, $event);
    }



    private function isSlotAvailable($startDatetime, $endDatetime)
    {
        $requestedSlot = [
            'start' => Carbon::parse($startDatetime),
            'end' => Carbon::parse($endDatetime),
        ];

        $existingAppointments = Appointment::all();

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

    private function doSlotsOverlap($slot1, $slot2)
    {
        return $slot1['start']->lte($slot2['end']) && $slot1['end']->gte($slot2['start']);
    }



    public function mount()
    {
        $this->prestations = Prestation::all();
        $this->employees = Employee::all();
        $this->setting = SalonSetting::first();
        $this->openDays = json_decode($this->setting->open_days, true);
    }

    public function updatedSelectedPrestations()
    {
        $this->getAvailableSlots();
    }

    public function updatedSelectedEmployee()
    {
        $this->getAvailableSlots();
    }

    public function getAvailableSlots()
    {
        if ($this->selectedPrestations && $this->selectedEmployee) {
            $employee = Employee::find($this->selectedEmployee);
            $employeeSchedules = EmployeeSchedule::where('employee_id', $employee->id)->get();

            $this->availableSlots = [];

            // Calculer la durée totale des prestations sélectionnées
            $totalDuration = 0;
            foreach ($this->selectedPrestations as $prestationId) {
                $prestation = Prestation::find($prestationId);
                $totalDuration += $prestation->temps;
            }

            // Déterminer la date de début (aujourd'hui) et la date de fin (dans un mois)
            $startDate = now();
            $endDate = now()->addMonth();

            while ($startDate <= $endDate) {
                $dayOfWeek = $startDate->format('w'); // 0 (dimanche) à 6 (samedi)

                // Vérifier si le jour est ouvert pour l'employé
                foreach ($employeeSchedules as $schedule) {
                    if ($schedule->day_of_week == $dayOfWeek) {
                        $openHours = $this->openDays[strtolower($startDate->format('l'))];
                        $scheduleStart = strtotime($schedule->start_time);
                        $scheduleEnd = strtotime($schedule->end_time);
                        $scheduleBreakStart = strtotime($schedule->break_start);
                        $scheduleBreakEnd = strtotime($schedule->break_end);
                        $shopBreakStart = strtotime($openHours['break_start']);
                        $shopBreakEnd = strtotime($openHours['break_end']);

                        $currentTime = max($scheduleStart, strtotime($openHours['open']));
                        while ($currentTime + $totalDuration * 60 <= min($scheduleEnd, strtotime($openHours['close']))) {
                            // Vérifier si le créneau n'est pas pendant la pause de l'employé
                            if ($currentTime + $totalDuration * 60 <= $scheduleBreakStart || $currentTime >= $scheduleBreakEnd) {
                                // Vérifier si le créneau n'est pas pendant la pause du salon
                                if ($currentTime + $totalDuration * 60 <= $shopBreakStart || $currentTime >= $shopBreakEnd) {
                                    $startDatetime = new DateTime($startDate->format('Y-m-d') . ' ' . date('H:i', $currentTime));
                                    $endDatetime = clone $startDatetime;
                                    $endDatetime->add(new DateInterval('PT' . $totalDuration . 'M'));

                                    // Vérifier si le créneau est disponible
                                    if ($this->isSlotAvailable($startDatetime, $endDatetime)) {
                                        $slotStart = date('H:i', $currentTime);
                                        $slotEnd = date('H:i', $currentTime + $totalDuration * 60);
                                        $this->availableSlots[] = [
                                            'start' => $slotStart,
                                            'end' => $slotEnd,
                                            'date' => $startDate->format('Y-m-d'),
                                        ];
                                    }
                                }
                            }
                            $currentTime += 3600;
                        }
                    }
                }

                $startDate->addDay(); // Passer au jour suivant
            }
        }
    }
    public function deletePrestation($index)
    {
        unset($this->selectedPrestations[$index]);
        $this->selectedPrestations = array_values($this->selectedPrestations);
        $this->getAvailableSlots();
    }

    public function toggleAddPrestationDiv()
    {
        $this->showAddPrestationDiv = !$this->showAddPrestationDiv;
    }
    public function book()
    {
        // Logique pour enregistrer la réservation
        // Vous pouvez par exemple créer un nouvel enregistrement dans la table des réservations
    }

    public function bookSlot()
    {
        //
    }

    public function getSelectedPrestations()
    {
        $selectedPrestations = [];
        foreach ($this->selectedPrestations as $prestationId) {
            $prestation = Prestation::find($prestationId);
            $selectedPrestations[] = [
                'id' => $prestation->id,
                'name' => $prestation->nom,
                'temps' => $prestation->temps,
                'prix' => $prestation->prix,
            ];
        }
        return $selectedPrestations;
    }

    public function togglePrestation($prestationId)
    {
        if (in_array($prestationId, $this->selectedPrestations)) {
            $key = array_search($prestationId, $this->selectedPrestations);
            unset($this->selectedPrestations[$key]);
            $this->selectedPrestations = array_values($this->selectedPrestations);
        } else {
            $this->selectedPrestations[] = $prestationId;
        }
        $this->getAvailableSlots();
        $this->toggleAddPrestationDiv();
    }

    public function render()
    {
        return view('livewire.reservation-component');
    }
}
