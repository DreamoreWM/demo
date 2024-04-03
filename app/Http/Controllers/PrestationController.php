<?php


namespace App\Http\Controllers;

use App\Models\Prestation;
use Illuminate\Http\Request;

class PrestationController extends Controller
{
    public function index()
    {
        $prestations = Prestation::all();
        return view('prestations.create', compact('prestations'));
    }

    public function create()
    {
        return view('prestations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'description' => 'required',
            'prix' => 'required|numeric',
            'temps' => 'required|integer',
        ]);

        Prestation::create($request->all());
        return redirect()->route('prestations.index')
            ->with('success', 'Prestation créée avec succès.');
    }

    public function show(Prestation $prestation)
    {
        return view('prestations.show', compact('prestation'));
    }

    public function edit(Prestation $prestation)
    {
        return view('prestations.edit', compact('prestation'));
    }

    public function update(Request $request, Prestation $prestation)
    {
        $request->validate([
            'nom' => 'required',
            'description' => 'required',
            'prix' => 'required|numeric',
            'temps' => 'required|integer',
        ]);

        $prestation->update($request->all());
        return redirect()->route('prestations.index')
            ->with('success', 'Prestation mise à jour avec succès.');
    }

    public function destroy(Prestation $prestation)
    {
        $prestation->delete();
        return redirect()->route('prestations.index')
            ->with('success', 'Prestation supprimée avec succès.');
    }
}
