<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use App\Models\Prestation;

class PrestationsManagement extends Component
{
    public $nom, $description, $prix, $temps, $category_id;
    public $prestations, $categories;
    protected $listeners = ['categoryDeleted' => 'refreshCategories', 'categoryAdded' => 'handleCategoryAdded'];

    public function mount()
    {
        $this->prestations = Prestation::all();
        $this->categories = Category::all(); // Ajoutez cette ligne

    }

    #[On('categoryAdded')]
    public function handleCategoryAdded()
    {
        session()->flash('message', 'Une nouvelle catégorie a été ajoutée.');
        $this->categories = Category::all(); // Recharger les catégories après l'ajout
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::find($categoryId);

        if ($category) {
            if ($category->prestations->count() > 0) {
                session()->flash('error', 'La catégorie ne peut pas être supprimée car elle a des prestations associées.');
            } else {
                $category->delete();
                $this->categories = Category::all(); // Recharger les catégories après suppression

                session()->flash('message', 'Catégorie supprimée avec succès.');
            }
        } else {
            session()->flash('error', 'La catégorie n\'a pas pu être trouvée.');
        }

        $this->dispatch('categoryDeleted');
    }

    public function addPrestation()
    {
        $this->validate([
            'nom' => 'required',
            'description' => 'required',
            'prix' => 'required|numeric',
            'temps' => 'required|integer',
            'category_id' => 'required', // Utilisez category_id pour associer la prestation à une catégorie

        ]);

        Prestation::create([
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'temps' => $this->temps,
            'category_id' => $this->category_id, // Utilisez category_id pour associer la prestation à une catégorie
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
