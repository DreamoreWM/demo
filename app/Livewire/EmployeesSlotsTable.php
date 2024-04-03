<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeesSlotsTable extends Component
{
    use WithPagination;


    public $search = '';
    public $perPage = 5;

    public function render()
    {
        return view('livewire.employees-slots-table',
        [
            'employees' => Employee::search($this->search)->paginate($this->perPage)
        ]);
    }

    public function delete(Employee $employee)
    {
        $employee->delete();
    }


}
