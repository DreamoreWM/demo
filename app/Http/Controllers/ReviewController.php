<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        $appointmentId = $request->query('appointment_id');
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            abort(404);
        }

        return view('reviews.create', ['appointmentId' => $appointmentId]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'rating' => 'required|integer|min:0|max:5','required_without:comment',
            'comment' => 'nullable|string','required_without:rating',
        ]);

        $review = new Review;
        $review->appointment_id = $request->appointment_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        return redirect('/');
    }

    public function index()
    {
        $reviews = Review::all();
        return view('reviews.index', ['reviews' => $reviews]);
    }
}
