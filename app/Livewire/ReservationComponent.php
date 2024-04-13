<?php

namespace App\Livewire;

use App\Models\EmployeeSchedule;
use App\Models\SalonSetting;
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
    public $showAddPrestationDiv = false;

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

            while ($startDate->lte($endDate)) {
                $dayOfWeek = strtolower($startDate->format('l')); // 1 (lundi) à 7 (dimanche)

                // Vérifier si le jour est ouvert
                if (isset($this->openDays[$dayOfWeek])) {
                    $openHours = $this->openDays[$dayOfWeek];
                    $startTime = strtotime($openHours['open']);
                    $endTime = strtotime($openHours['close']);
                    $breakStart = strtotime($openHours['break_start']);
                    $breakEnd = strtotime($openHours['break_end']);

                    $currentTime = $startTime;

                    while ($currentTime + $totalDuration * 60 <= $endTime) {
                        // Vérifier si le créneau n'est pas pendant la pause
                        if ($currentTime + $totalDuration * 60 <= $breakStart || $currentTime >= $breakEnd) {
                            $slotStart = date('H:i', $currentTime);
                            $slotEnd = date('H:i', $currentTime + $totalDuration * 60); // Durée totale des prestations
                            $this->availableSlots[] = [
                                'start' => $slotStart,
                                'end' => $slotEnd,
                                'date' => $startDate->format('Y-m-d'),
                            ];
                        }

                        $currentTime += 3600; // Passer au créneau suivant (1 heure)
                    }
                }

                // Passer au jour suivant
                $startDate->addDay();
            }
        }
    }

    public function book()
    {
        // Logique pour enregistrer la réservation
        // Vous pouvez par exemple créer un nouvel enregistrement dans la table des réservations
    }

    public function render()
    {
        return view('livewire.reservation-component');
    }
}
