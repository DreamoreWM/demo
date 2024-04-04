<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class EmployeeScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $schedules = $employee->schedules()->get();
        return view('employees.schedule', compact('employee', 'schedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Employee $employee)
    {
        try {
            $validatedData = $request->validate([
                'schedules.*.start_time' => 'nullable|date_format:H:i',
                'schedules.*.end_time' => 'nullable|required_with:schedules.*.start_time|date_format:H:i|after:schedules.*.start_time',
                'schedules.*.day_of_week' => 'nullable|integer|between:1,7',
                'schedules.*.id' => 'nullable|exists:employee_schedules,id',
            ]);

            foreach ($validatedData['schedules'] as $scheduleData) {
                // If start_time or end_time is not set, skip this day of week
                if (!isset($scheduleData['start_time']) || !isset($scheduleData['end_time'])) {
                    continue;
                }

                if (isset($scheduleData['id'])) {
                    // Update existing schedule
                    $schedule = $employee->schedules()->find($scheduleData['id']);
                    if ($schedule) {
                        $schedule->update([
                            'start_time' => $scheduleData['start_time'],
                            'end_time' => $scheduleData['end_time']
                        ]);
                    }
                } else {
                    // Create new schedule
                    $employee->schedules()->create([
                        'day_of_week' => $scheduleData['day_of_week'],
                        'start_time' => $scheduleData['start_time'],
                        'end_time' => $scheduleData['end_time']
                    ]);
                }
            }

            return back()->with('success', 'Schedule updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage());
            return back()->withErrors('An error occurred while updating the schedule.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deleteSchedules(Employee $employee): void
    {
        $employee->schedules()->delete();
    }
}
