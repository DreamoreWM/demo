<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Slot;
use Carbon\Carbon;
use Livewire\Component;

class EmployeeCalendar extends Component
{
    public $currentDate;
    public $selectedEmployeeId;
    public $showAddSlotModal = false;
    public $newSlotData = [
        'dayOfWeek' => '',
        'startTime' => '',
        'endTime' => '',
    ];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->selectedEmployeeId = Employee::first()->id;
    }

    public function render()
    {
        $employees = Employee::all();
        $slots = Slot::where('employee_id', $this->selectedEmployeeId)
            ->whereDate('date', '>=', $this->currentDate->startOfMonth())
            ->whereDate('date', '<=', $this->currentDate->endOfMonth())
            ->get()
            ->groupBy(function ($slot) {
                return Carbon::parse($slot->date)->format('Y-m-d');
            });

        return view('livewire.employee-calendar', compact('employees', 'slots'));
    }

    public function changeDate($newDate)
    {
        $this->currentDate = Carbon::parse($newDate);
    }

    public function changeEmployee($employeeId)
    {
        $this->selectedEmployeeId = $employeeId;
    }

    public function showAddSlotModal($dayOfWeek, $date)
    {
        $this->newSlotData = [
            'dayOfWeek' => $dayOfWeek,
            'startTime' => '09:00',
            'endTime' => '10:00',
        ];
        $this->showAddSlotModal = true;
    }

    public function addNewSlot()
    {
        $validatedData = $this->validate([
            'newSlotData.startTime' => 'required|date_format:H:i',
            'newSlotData.endTime' => 'required|date_format:H:i|after:newSlotData.startTime',
        ]);

        $date = Carbon::parse($this->currentDate->format('Y-m-d') . ' ' . $validatedData['newSlotData']['dayOfWeek']);

        Slot::create([
            'employee_id' => $this->selectedEmployeeId,
            'day_of_week' => $validatedData['newSlotData']['dayOfWeek'],
            'date' => $date,
            'start_time' => $validatedData['newSlotData']['startTime'],
            'end_time' => $validatedData['newSlotData']['endTime'],
        ]);

        $this->showAddSlotModal = false;
    }
}
