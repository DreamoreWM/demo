<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Prestation;
use App\Models\Slot;
use App\Models\User;
use DateTime;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Livewire\Component;

class AppointmentForm extends Component
{

    public $selectedPrestation; // Pour stocker la prestation actuellement sélectionnée
    public $selectedPrestations = []; // Pour stocker toutes les prestations sélectionnées


    public $slots;
    public $prestations; // Ajout de la variable $prestations
    public $selectedItemIds;
    public $confirmingItem = false;
    public $selectedEmployeeId;
    public $currentWeekStartDate;
    public $currentWeekEndDate;
    public $showAddPrestationDiv = false;

    public function mount()
    {
        $this->selectedPrestation = null;
        $this->availableSlots = [];
        $this->prestations = Prestation::all();
        $this->currentWeekStartDate = now()->startOfWeek();
        $this->endOfMonthDate = now()->endOfMonth();
    }

    public function toggleAddPrestationDiv()
    {
        $this->showAddPrestationDiv = !$this->showAddPrestationDiv;
    }
  
    public function confirmItem($itemIds)
    {
        $this->selectedItemIds = $itemIds;
        $this->confirmingItem = true;
    }

    public function render()
    {
        $employees = Employee::all();
        return view('livewire.appointment-form', compact('employees'));
    }

    public function updatedSelectedEmployeeId($value)
    {
        $this->selectedEmployeeId = $value;
        $this->availableSlots = $this->getAvailableSlots($this->selectedPrestation->id, $value);
    }

    public function selectPrestation($prestationId)
    {
        $this->selectedPrestation = Prestation::findOrFail($prestationId);
            $this->selectedPrestations[] = $this->selectedPrestation;
            $this->selectedPrestations[] = $this->selectedPrestation;
        }
        $this->selectedPrestations[] = $this->selectedPrestation;
        }

        $firstEmployee = Employee::first();
        if ($firstEmployee) {
            $this->selectedEmployeeId = $firstEmployee->id;
        } else {
            $this->selectedEmployeeId = null;
        }

        $this->availableSlots = $this->getAvailableSlots($prestationId, $this->selectedEmployeeId);
        $this->showAddPrestationDiv = false;
    }

    private function getAvailableSlots($prestationId, $employeeId = null)
    {
        $totalDuration = array_sum(array_map(function($prestation) {
            return $prestation->temps; // Assurez-vous que 'temps' est en minutes
        }, $this->selectedPrestations));

        $availableSlots = [];
        $openDays = json_decode(setting('salon_settings.open_days'), true);
        $employeeSchedule = Employee::where('id', $employeeId)->first()->schedule;

        for ($date = $this->currentWeekStartDate; $date <= $this->endOfMonthDate; $date->addDay()) {
            $dayOfWeek = $date->format('l');
            if (isset($openDays[$dayOfWeek]) && $employeeSchedule[$dayOfWeek]['open'] != null) {
                $openTime = new DateTime($openDays[$dayOfWeek]['open']);
                $closeTime = new DateTime($openDays[$dayOfWeek]['close']);
                $breakStartTime = new DateTime($openDays[$dayOfWeek]['break_start']);
                $breakEndTime = new DateTime($openDays[$dayOfWeek]['break_end']);

                while ($openTime < $closeTime) {
                    if ($openTime >= $breakStartTime && $openTime < $breakEndTime) {
                        $openTime->modify('+1 hour'); // Sauter la pause déjeuner
                        continue;
                    }

                    $endTime = clone $openTime;
                    $endTime->modify("+{$totalDuration} minutes");
                    if ($endTime <= $closeTime) {
                        $availableSlots[] = [
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $openTime->format('H:i:s'),
                            'end_time' => $endTime->format('H:i:s'),
                            'employee_id' => $employeeId,
                        ];
                    }
                    $openTime->modify('+1 hour');
                }
            }
        }

        return $availableSlots;
    }

    public function bookSlot() {
        $userId = auth()->id(); // Assurez-vous que l'utilisateur est authentifié.
        $user = User::find($userId);

        // Supposons que getAvailableSlots renvoie une collection de slots disponibles sans tenir compte des prestations.
        $availableSlots = $this->getAvailableSlots(null, $this->selectedEmployeeId);

        $totalDuration = array_sum(array_map(function($prestation) {
            return $prestation->temps; // 'temps' est en minutes
        }, $this->selectedPrestations));

            // Supposons que $availableSlots soit correctement calculé avant cette boucle
        $slotsToBook = ceil($totalDuration / 60); // Nombre de créneaux nécessaires basé sur la durée totale
        $slotsBooked = 0; // Compteur pour le nombre de créneaux déjà réservés

        foreach ($this->selectedPrestations as $selectedPrestation) {
            foreach ($availableSlots as $date => $slotsOnDate) {
                foreach ($slotsOnDate as $slotGroup) {
                    foreach ($slotGroup as $slot) {
                        if ($slotsBooked < $slotsToBook) {
                            $appointment = new Appointment();
                        $appointment->bookable_id = $user->id;
                        $appointment->bookable_type = get_class($user);
                        $appointment->slot_id = $this->selectedItemIds; // Assurez-vous que $slot est l'instance correcte.
                        $this->selectedItemIds++;
                        $appointment->save();



                        $slot = Slot::find($slot['id']);
                        $slot->prestation_id = $selectedPrestation->id;
                        $slot->save();
                            $slotsBooked++; // Incrémentez après chaque réservation
                        }

                        if ($slotsBooked >= $slotsToBook) {
                            // Nous avons réservé le nombre nécessaire de créneaux, arrêtons la boucle
                            break 4; // Sort de toutes les boucles imbriquées
                        }
                    }
                }
            }
        }

        if ($slotsBooked < $slotsToBook) {
            // Gérer le cas où il n'y a pas assez de slots disponibles
            return redirect('/dashboard')->with('error', "Pas assez de créneaux disponibles pour les prestations sélectionnées.");
        }

        $this->addEventToGoogleCalendar($user, $slot);

        // Envoyer l'e-mail de confirmation à l'utilisateur
        \Mail::to($user->email)->send(new \App\Mail\ReservationConfirmed($user, $slot));

        // Supposons que chaque Slot a un employee_id
        $employee = Employee::where('id', $slot->employee_id)->firstOrFail();

        // Envoyer l'e-mail à l'employé pour notifier de la réservation
        \Mail::to($employee->email)->send(new \App\Mail\SlotBookedForEmployee($user, $slot));


        return redirect('/dashboard')->with('success', 'Le créneau a été réservé avec succès.');
    }

    private function addEventToGoogleCalendar($user, $slot)
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

        $startHour = $slot->start_time->format('H:i:s');
        $endHour = $slot->end_time->format('H:i:s');

        $newDate = $slot->date; // Format Y-m-d

      // Combinez la nouvelle date avec les heures extraites
        $startDateTime = $newDate . ' ' . $startHour;
        $endDateTime = $newDate . ' ' . $endHour;

         // Conversion en format RFC3339 pour l'API Google Calendar
        $startDateTimeRFC3339 = (new DateTime($startDateTime))->format(DateTime::RFC3339);
        $endDateTimeRFC3339 = (new DateTime($endDateTime))->format(DateTime::RFC3339);

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Rendez-vous Coiffeur',
            'start' => ['dateTime' => $startDateTimeRFC3339],
            'end' => ['dateTime' => $endDateTimeRFC3339],
        ]);

        $calendarId = 'primary';
        $service->events->insert($calendarId, $event);
    }


    public function openConfirmModal($slotId)
    {
        $this->selectedSlotId = $slotId;
        $this->showConfirmationModal = true;
    }

    public function deletePrestation($index)
    {
        if (isset($this->selectedPrestations[$index])) {
            unset($this->selectedPrestations[$index]);
            $this->selectedPrestations = array_values($this->selectedPrestations);
            $this->recalculateAvailableSlots();
        }
    }

    public function recalculateAvailableSlots()
    {
        if (count($this->selectedPrestations) > 0) {
            $totalDuration = array_sum(array_map(function($prestation) {
                return $prestation->temps; // 'temps' est en minutes
            }, $this->selectedPrestations));

            $this->availableSlots = $this->getAvailableSlots($this->selectedPrestations[0]->id, $this->selectedEmployeeId);
        } else {
            $this->selectedPrestation = null;
            $this->availableSlots = [];
        }
    }
}
