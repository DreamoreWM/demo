<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class AddCategory extends Component
{
    public $name;

    public function addCategory()
    {
        $this->validate([
            'name' => 'required',
        ]);

        Category::create([
            'name' => $this->name,
        ]);

        $this->dispatch('categoryAdded');

        session()->flash('message', 'Catégorie ajoutée avec succès.');

    }

    public function render()
    {
        return view('livewire.add-category');
    }
}
