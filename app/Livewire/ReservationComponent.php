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
    public $selectedPrestation;
    public $selectedEmployee;
    public $availableSlots = [];

    public function mount()
    {
        $this->prestations = Prestation::all();
        $this->employees = Employee::all();
        $this->setting = SalonSetting::first();
        $this->openDays = json_decode($this->setting->open_days, true);
    }

    public function updatedSelectedPrestation()
    {
        $this->getAvailableSlots();
    }

    public function updatedSelectedEmployee()
    {
        $this->getAvailableSlots();
    }

    public function getAvailableSlots()
    {
        if ($this->selectedPrestation && $this->selectedEmployee) {
            $prestation = Prestation::find($this->selectedPrestation);
            $employee = Employee::find($this->selectedEmployee);
            $employeeSchedules = EmployeeSchedule::where('employee_id', $employee->id)->get();

            $this->availableSlots = [];

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

                    while ($currentTime < $endTime) {
                        $slotStart = date('H:i', $currentTime);
                        $slotEnd = date('H:i', $currentTime + 3600); // Ajout d'une heure

                        // Vérifier si le créneau n'est pas pendant la pause
                        if ($currentTime < $breakStart || $currentTime >= $breakEnd) {
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
