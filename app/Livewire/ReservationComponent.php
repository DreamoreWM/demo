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
                                    $slotStart = date('H:i', $currentTime);
                                    $slotEnd = date('H:i', $currentTime + $totalDuration * 60);
                                    $this->availableSlots[] = [
                                        'start' => $slotStart,
                                        'end' => $slotEnd,
                                        'date' => $startDate->format('Y-m-d'),
                                    ];
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

    public function render()
    {
        return view('livewire.reservation-component');
    }
}
