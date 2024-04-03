<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersSlotTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    public $selectedUser = null;

    public function selectUser($userId) {
        $this->selectedUser = User::find($userId);
        dd($this->selectedUser);
    }


    public function render()
    {
        $users = User::search($this->search)
            ->with('appointments')
            ->paginate($this->perPage);

        return view('livewire.users-slot-table', ['users' => $users]);
    }

    public function delete(User $users)
    {
        $users->delete();
    }

    public function openModal($userId)
    {
        // Dispatching global event to open the modal with user ID
        $this->dispatch('openUserModal', ['userId' => $userId]);
    }

}
