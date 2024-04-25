<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $reviews = Review::with('appointment.bookable')->with('photo')->get();
        return view('dashboard', ['reviews' => $reviews]);
    }
}
