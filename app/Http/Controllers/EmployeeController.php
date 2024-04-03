<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Afficher la liste des employés.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employees.manage-employees', compact('employees'));
    }

    /**
     * Stocker un nouvel employé dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            // Utilisez la règle 'email' pour valider le champ email
            'email' => 'required|string|email|max:255|unique:employees,email',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Coiffeur ajouté avec succès.');
    }

    /**
     * Supprimer un employé spécifié.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return back()->with('success', 'Coiffeur supprimé avec succès.');
    }

    public function getSlotsForEmployee($employeeId)
    {
        return Slot::where('employee_id', $employeeId)->get();
    }


    public function edit($id)
    {
        $employee = Employee::with(['slots.appointment'])->findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->only(['name', 'email']));
        return redirect()->back()->with('success', 'Informations mises à jour avec succès.');
    }


}
