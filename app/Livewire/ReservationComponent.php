<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\EmployeeSchedule;
use App\Models\SalonSetting;
use App\Models\User;
use DateInterval;
use DateTime;
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

        // Réinitialiser les données
        $this->selectedPrestations = [];
        $this->selectedEmployee = null;
        $this->selectedSlot = null;
        $this->availableSlots = [];

        return redirect('/dashboard')->with('success', 'Le créneau a été réservé avec succès.');
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
            $employeeSchedules = EmployeeSchedule::where('employee_id', $employee->id)
                ->whereBetween('day_of_week', [0, 6])
                ->get();

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

            while ($startDate->lte($endDate)) {
                $dayOfWeek = $startDate->format('w'); // 0 (dimanche) à 6 (samedi)

                // Récupérer les rendez-vous de l'employé pour la date actuelle
                $appointments = Appointment::where('employee_id', $employee->id)
                    ->whereDate('start_time', $startDate->format('Y-m-d'))
                    ->get();

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
                                    // Vérifier si le créneau n'est pas déjà réservé
                                    $isAvailable = true;
                                    foreach ($appointments as $appointment) {
                                        $appointmentStart = strtotime($appointment->start_time);
                                        $appointmentEnd = strtotime($appointment->end_time);
                                        if ($currentTime >= $appointmentStart && $currentTime < $appointmentEnd) {
                                            $isAvailable = false;
                                            break;
                                        }
                                    }
                                    if ($isAvailable && $currentTime >= time()) {
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
                            $currentTime += 3600; // Passer au créneau suivant (1 heure)
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
