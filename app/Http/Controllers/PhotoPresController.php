<?php

namespace App\Http\Controllers;

use App\Models\Photospres;
use Illuminate\Http\Request;

class PhotoPresController extends Controller
{
    public function index()
    {
        $photos = Photospres::all();
        return view('photos.index', compact('photos'));
    }

    public function store(Request $request)
    {
        $photo = new Photospres;
        $photo->path = $request->file('photo')->store('photosPres', 'public');
        $photo->save();

        return redirect()->route('photos.index');
    }
}
