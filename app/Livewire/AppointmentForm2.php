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

class AppointmentForm2 extends Component
{

    public $selectedPrestation; // Pour stocker la prestation actuellement sélectionnée
    public $selectedPrestations = []; // Pour stocker toutes les prestations sélectionnées


    public $slots;
    public $prestations; // Ajout de la variable $prestations
    public $selectedItemIds;
    public $confirmingItemDeletion = false;
    public $selectedEmployeeId;
    public $currentWeekStartDate;
    public $currentWeekEndDate;

    public $showAddPrestationDiv = false;

    public function mount()
    {
        $this->selectedPrestation = null;
        $this->slots = [];
        $this->prestations = Prestation::all(); // Récupération de toutes les prestations
        $this->currentWeekStartDate = now()->startOfWeek();
        // Utilisez endOfMonth() pour le calcul initial de la fin, mais la logique de boucle ajustera cela pour chaque semaine
        $this->endOfMonthDate = now()->endOfMonth();
    }

    public function toggleAddPrestationDiv()
    {
        $this->showAddPrestationDiv = !$this->showAddPrestationDiv;
    }


    public function confirmItemDeletion($itemIds)
    {
        $this->selectedItemIds = $itemIds;
        $this->confirmingItemDeletion = true;
    }




    public function render()
    {
        $employees = Employee::all();
        return view('livewire.appointment-form', compact('employees'));
    }

    public function updatedSelectedEmployeeId($value)
    {
            $this->selectedEmployeeId = $value;
            $this->slots = $this->getAvailableSlots($this->selectedPrestation->id, $value);
    }



    public function selectPrestation($prestationId)
    {
        // Trouver la prestation sélectionnée et la stocker dans $selectedPrestation
        $this->selectedPrestation = Prestation::findOrFail($prestationId);

        // Ajouter la prestation sélectionnée au tableau des prestations sélectionnées
        // Vérifier d'abord si la prestation n'est pas déjà dans le tableau pour éviter les doublons
        if (!in_array($this->selectedPrestation, $this->selectedPrestations)) {
            $this->selectedPrestations[] = $this->selectedPrestation;
        }

        // Sélectionner automatiquement le premier employé de la liste
        $firstEmployee = Employee::first();
        if ($firstEmployee) {
            $this->selectedEmployeeId = $firstEmployee->id;
        } else {
            $this->selectedEmployeeId = null;
        }

        // Charger les créneaux pour cet employé
        // Note: Vous pourriez vouloir ajuster cette logique pour prendre en compte toutes les prestations sélectionnées
        $this->slots = $this->getAvailableSlots($prestationId, $this->selectedEmployeeId);
        $this->showAddPrestationDiv = false;
    }


    private function getAvailableSlots($prestationId, $employeeId = null)
    {
        // Récupérer la durée de la prestation
        $totalDuration = array_sum(array_map(function($prestation) {
            return $prestation->temps; // Assurez-vous que 'temps' est en minutes
        }, $this->selectedPrestations));


        // Filtrer les créneaux par employé, si un ID est fourni
        $creneauxDisponibles = Slot::when($employeeId, function ($query) use ($employeeId) {
            return $query->where('employee_id', $employeeId);
        })->orderBy('date')->orderBy('start_time')->get();

        // Filtrer pour obtenir des créneaux consécutifs
        $creneauxConsecutifs = $this->filterConsecutiveSlots($creneauxDisponibles, $totalDuration, $employeeId);

        return collect($creneauxConsecutifs)->groupBy('date');
    }



    private function filterConsecutiveSlots($creneauxDisponibles, $dureePrestation, $employeeId)
    {
        $totalSlotsNeeded = ceil($dureePrestation / 60); // Calculer le nombre total de créneaux nécessaires

        $creneauxConsecutifs = [];

        $sequenceConsecutive = collect(); // Initialiser une collection pour stocker la séquence consécutive de créneaux

        foreach ($creneauxDisponibles as $creneau) {
            // S'assurer que le créneau appartient à l'employé spécifié
            if ($creneau->employee_id != $employeeId) {
                continue; // Passer au prochain créneau si l'ID de l'employé ne correspond pas
            }

            // Vérifier si le créneau est déjà réservé
            $isReserved = \App\Models\Appointment::where('slot_id', $creneau->id)->exists();

            if (!$isReserved) {
                // La logique existante pour vérifier si le créneau est consécutif
                if ($sequenceConsecutive->isEmpty() || $creneau->start_time == $sequenceConsecutive->last()->end_time) {
                    if ($sequenceConsecutive->isEmpty() || $creneau->day_of_week == $sequenceConsecutive->first()->day_of_week) {
                        $sequenceConsecutive->push($creneau);
                    } else {
                        $sequenceConsecutive = collect([$creneau]);
                    }
                } else {
                    $sequenceConsecutive = collect([$creneau]);
                }

                if ($sequenceConsecutive->count() == $totalSlotsNeeded) {
                    $creneauxConsecutifs[] = $sequenceConsecutive->toArray();
                    $sequenceConsecutive = collect();
                }
            } else {
                // Si le créneau est réservé, réinitialiser la séquence consécutive
                // seulement si la séquence actuelle ne satisfait pas déjà le besoin en créneaux
                if ($sequenceConsecutive->count() < $totalSlotsNeeded) {
                    $sequenceConsecutive = collect();
                }
            }
        }

        return $creneauxConsecutifs;
    }


    public function updatedSelectedEmployee($employeeId)
    {

        $usedSlotIds = Appointment::pluck('slot_id')->toArray(); // Récupère tous les slotId de la table appointments

        // Récupérer les slots de l'employé sélectionné qui ne sont pas pris
        $this->slots = Slot::where('employee_id', $employeeId)
            ->whereNotIn('id', $usedSlotIds) // Exclut les slots déjà pris
            ->get();
    }

    public function bookSlot()
    {
        $firstSlotId = $this->selectedItemIds;

        $userId = auth()->id(); // Récupère l'ID de l'utilisateur actuellement authentifié
        $totalMinutes = array_sum(array_map(function($prestation) {
            return $prestation->temps; // Assurez-vous que 'temps' est en minutes
        }, $this->selectedPrestations));

        $totalSlotsNeeded = ceil($totalMinutes / 60);

    $currentSlot = Slot::findOrFail($firstSlotId); // Trouvez le premier créneau
    $slotsBooked = 0; // Compteur pour les créneaux réservés

    while ($slotsBooked < $totalSlotsNeeded) {
        // Vérifiez si le créneau actuel n'est pas déjà réservé
        if (!Appointment::where('slot_id', $currentSlot->id)->exists()) {
            Appointment::create([
                'slot_id' => $currentSlot->id,
                'user_id' => $userId,
            ]);
            $slotsBooked++;
        } else {
            // Si le créneau est déjà réservé, cela pourrait indiquer un problème, car vous vous attendiez à des créneaux libres
            break;
        }

        // Trouvez le prochain créneau consécutif basé sur l'heure de fin du créneau actuel
        $nextSlot = Slot::where('employee_id', $currentSlot->employee_id)
            ->where('start_time', $currentSlot->end_time)
            ->first();
        if (!$nextSlot) {
            // S'il n'y a pas de prochain créneau, arrêtez la boucle
            break;
        }
        $currentSlot = $nextSlot; // Préparez le prochain créneau pour la vérification/la réservation
    }
//        $itemId = $this->selectedItemId;
//
//        $userId = auth()->id();
//        $user = User::findOrFail($userId);
//        $slot = Slot::findOrFail($itemId);
//
//        // Créer un nouveau rendez-vous
//        Appointment::create([
//            'slot_id' => $itemId,
//            'user_id' => $userId,
//        ]);

//        $this->addEventToGoogleCalendar($user, $slot);
//
//        // Envoyer l'e-mail de confirmation à l'utilisateur
//        \Mail::to($user->email)->send(new \App\Mail\ReservationConfirmed($user, $slot));
//
//        // Supposons que chaque Slot a un employee_id
//        $employee = Employee::where('id', $slot->employee_id)->firstOrFail();
//
//        // Envoyer l'e-mail à l'employé pour notifier de la réservation
//        \Mail::to($employee->email)->send(new \App\Mail\SlotBookedForEmployee($user, $slot));

        // Rediriger vers l'accueil avec un message de succès
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
        unset($this->selectedPrestations[$index]);
        // Réindexer le tableau pour éviter les problèmes avec les indices manquants
        $this->selectedPrestations = array_values($this->selectedPrestations);
    }








}
