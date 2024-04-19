<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Employee;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index()
    {
        $absences = Absence::with('employee')->get();
        return view('absences.index', compact('absences'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('absences.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $validatedData['start_time'] = \Carbon\Carbon::parse($request->start_time);
        $validatedData['end_time'] = \Carbon\Carbon::parse($request->end_time);

        $absence = Absence::create($validatedData);

        return redirect()->route('absences.index');
    }

    public function edit(Absence $absence)
    {
        $employees = Employee::all();
        return view('absences.edit', compact('absence', 'employees'));
    }

    public function update(Request $request, Absence $absence)
    {
        $absence->update($request->all());
        return redirect()->route('absences.index');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        return redirect()->route('absences.index');
    }
}
