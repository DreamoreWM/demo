<?php

namespace App\Livewire;

use App\Models\Appointment;
use Livewire\Component;

class SlotsManagement extends Component
{
    public $employee;

    public $confirmingItemDeletion = false;

    public function mount($employee)
    {
        $this->employee = $employee;
    }

    public function render()
    {
        return view('livewire.slots-management');
    }


    public function confirmItemDeletion()
    {
        $this->confirmingItemDeletion = true;
    }

    public function succes(){
        return redirect('/dashboard')->with('success', 'Le créneau a été réservé avec succès.');
    }

    public function saveSchedule(Request $request)
    {
        foreach ($request->start_time as $date => $start_time) {
            $end_time = $request->end_time[$date];
            // Calculate hourly slots
            $this->createSlots($date, $start_time, $end_time);
        }
        // redirect or return response
    }

    private function createSlots($date, $start_time, $end_time)
    {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $start_time);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $end_time);

        while ($start < $end) {
            // Create a slot
            $this->employee->slots()->create([
                'date' => $date,
                'start_time' => $start->format('H:i:s'),
                'end_time' => $start->addHour()->format('H:i:s'),
            ]);
        }
    }

}
