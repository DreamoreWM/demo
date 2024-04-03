<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalonSetting;

class SalonController extends Controller
{
    // Méthode pour afficher le formulaire de paramètres du salon
    public function edit()
    {
        // Récupère les paramètres existants ou crée un nouvel objet sans le sauvegarder
        $setting = SalonSetting::firstOrNew([]);

        // Passe les paramètres (existants ou nouveaux) à la vue
        return view('salon.edit', compact('setting'));
    }

    // Méthode pour sauvegarder ou mettre à jour les paramètres
    public function update(Request $request)
    {
        $data = $request->all();
        $data['open_days'] = json_encode($data['open_days']);

        // Récupère le premier enregistrement de paramètres ou en crée un nouveau
        $setting = SalonSetting::firstOrNew([]);
        $setting->fill($data)->save();

        return redirect()->route('salon.edit')->with('success', 'Les paramètres ont été mis à jour avec succès.');
    }
}
