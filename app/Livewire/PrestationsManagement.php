<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Prestation;

class PrestationsManagement extends Component
{
    public $nom, $description, $prix, $temps;
    public $prestations;

    public function mount()
    {
        $this->prestations = Prestation::all();
    }

    public function addPrestation()
    {
        $this->validate([
            'nom' => 'required',
            'description' => 'required',
            'prix' => 'required|numeric',
            'temps' => 'required|integer',
        ]);

        Prestation::create([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'temps' => $this->temps,
        ]);

        $this->prestations = Prestation::all(); // Recharger les prestations

        $this->resetErrorBag();

        session()->flash('message', 'Prestation ajoutée avec succès.');
    }

    public function deletePrestation($prestationId)
    {
        $this->resetErrorBag();

        $prestation = Prestation::find($prestationId);

        if ($prestation) {
            $prestation->delete();
            $this->prestations = Prestation::all(); // Recharger les prestations après suppression

            session()->flash('message', 'Prestation supprimée avec succès.');
        } else {

            session()->flash('error', 'La prestation n\'a pas pu être trouvée.');
        }
    }

    public function render()
    {
        return view('livewire.prestations-management');
    }
}
