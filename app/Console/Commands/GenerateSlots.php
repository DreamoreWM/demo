<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Employee, EmployeeSchedule, Slot, SalonSetting};
use Carbon\Carbon;

class GenerateSlots extends Command
{
    protected $signature = 'generate:slots';
    protected $description = 'Generate slots for each employee for 1 month from today.';

    public function handle()
    {
        $settings = SalonSetting::first();
        if (!$settings) {
            $this->error('Salon settings not found.');
            return;
        }

        $endDate = now()->addMonth();
        $employees = Employee::all();

        foreach ($employees as $employee) {
            for ($date = now(); $date->lte($endDate); $date->addDay()) {
                $dayOfWeekName = strtolower($date->englishDayOfWeek);

                $openDays = json_decode($settings->open_days, true);
                if (!isset($openDays[$dayOfWeekName])) continue;

                $daySettings = $openDays[$dayOfWeekName];
                $employeeSchedule = $employee->schedules()->where('day_of_week', $date->dayOfWeekIso)->first();
                if (!$employeeSchedule) continue;

                // Créer des créneaux pour avant et après la pause midi
                $periods = [
                    ['start' => $daySettings['open'], 'end' => $daySettings['break_start']],
                    ['start' => $daySettings['break_end'], 'end' => $daySettings['close']],
                ];

                foreach ($periods as $period) {
                    $startTime = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . $period['start']);
                    $endTime = Carbon::createFromFormat('Y-m-d H:i', $date->toDateString() . ' ' . $period['end']);

                    while ($startTime->lt($endTime)) {
                        $nextStartTime = (clone $startTime)->addMinutes($settings->slot_duration);
                        if ($nextStartTime->gt($endTime)) break; // Ne pas créer de créneau si cela dépasse l'heure de fin

                        Slot::firstOrCreate([
                            'employee_id' => $employee->id,
                            'date' => $date->toDateString(),
                            'day_of_week' => $date->dayOfWeekIso, // Utiliser dayOfWeekIso pour la cohérence
                            'start_time' => $startTime->format('H:i:s'),
                            'end_time' => $nextStartTime->format('H:i:s'),
                        ]);

                        $startTime = $nextStartTime;
                    }
                }
            }
        }

        $this->info('Slots generated successfully for the next month.');
    }




}
