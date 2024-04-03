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
        $totalSlotsNeeded = ceil($dureePrestation / 60); // Nombre total de créneaux nécessaires
        $creneauxConsecutifs = [];

        // Convertissez d'abord la collection en tableau
        $creneauxArray = $creneauxDisponibles->toArray();

        for ($i = 0; $i < count($creneauxArray); $i++) {
            $sequenceConsecutive = collect();
            foreach (array_slice($creneauxArray, $i) as $creneau) {
                if ($creneau['employee_id'] != $employeeId || \App\Models\Appointment::where('slot_id', $creneau['id'])->exists()) {
                    break; // S'il ne s'agit pas du bon employé ou si le créneau est déjà réservé, arrêtez cette séquence
                }

                if (!$sequenceConsecutive->isEmpty() && $creneau['start_time'] != $sequenceConsecutive->last()['end_time']) {
                    break; // Si le créneau n'est pas consécutif à la séquence, arrêtez cette séquence
                }

                $sequenceConsecutive->push($creneau);
                if ($sequenceConsecutive->count() == $totalSlotsNeeded) {
                    $creneauxConsecutifs[] = $sequenceConsecutive->toArray();
                    break; // Trouvé un ensemble satisfaisant, continuez à chercher d'autres ensembles
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


//    public function bookSlot()
//    {
//        $userId = auth()->id(); // Assurez-vous que l'utilisateur est authentifié.
//        $user = User::find($userId);
//
//        // Supposons que getAvailableSlots renvoie une collection de slots disponibles sans tenir compte des prestations.
//        $availableSlots = $this->getAvailableSlots(null, $this->selectedEmployeeId);
//
//        foreach ($this->selectedPrestations as $selectedPrestation) {
//            $duration = $selectedPrestation->temps; // Durée de la prestation actuelle en minutes.
//            $slotsNeeded = ceil($duration / 60); // Calculez le nombre de slots nécessaires, en supposant que chaque slot représente une heure.
//
//            $slotsBooked = 0; // Compteur pour les slots réservés pour cette prestation.
//
//            foreach ($availableSlots as $date => $slotsOnDate) {
//                foreach ($slotsOnDate as $slotGroup) {
//                    foreach ($slotGroup as $slot) {
//                        if ($slotsBooked >= $slotsNeeded) {
//                            break 3; // Si nous avons réservé assez de slots pour la prestation, passez à la prochaine prestation.
//                        }
//
//                        // Créez l'Appointment en associant le slot et l'utilisateur.
//                        $appointment = new Appointment();
//                        $appointment->bookable_id = $user->id;
//                        $appointment->bookable_type = get_class($user);
//                        $appointment->slot_id = $this->selectedItemIds; // Assurez-vous que $slot est l'instance correcte.
//                        $this->selectedItemIds++;
//                        $appointment->save();
//
//
//
//                        $slot = Slot::find($slot['id']);
//                        $slot->prestation_id = $selectedPrestation->id;
//                        $slot->save();
//
//                        $slotsBooked++; // Incrémentez le compteur de slots réservés.
//                    }
//                }
//            }
//
//            if ($slotsBooked < $slotsNeeded) {
//                // Gérer le cas où il n'y a pas assez de slots disponibles pour la prestation actuelle.
//                return redirect('/dashboard')->with('error', "Pas assez de créneaux disponibles pour les prestations sélectionnées.");
//            }
//        }
//

//        // Rediriger vers l'accueil avec un message de succès
//        return redirect('/dashboard')->with('success', 'Le créneau a été réservé avec succès.');
//    }

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
        $this->selectedPrestations = array_values($this->selectedPrestations); // Réindexe le tableau

        // Recalculez les créneaux disponibles ici
        $this->recalculateAvailableSlots();
    }

    public function recalculateAvailableSlots()
    {
        $totalDuration = array_sum(array_map(function($prestation) {
            return $prestation->temps; // 'temps' est en minutes
        }, $this->selectedPrestations));

        // Mettez à jour $this->slots en fonction des prestations restantes
        $this->slots = $this->getAvailableSlots($this->selectedPrestations[0]->id, $this->selectedEmployeeId, $totalDuration);
    }
}
