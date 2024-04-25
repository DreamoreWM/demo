<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Photo;
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
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // validation for images
            'comment' => 'nullable|string','required_without:rating',
        ]);

        $review = new Review;
        $review->appointment_id = $request->appointment_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        if($request->hasfile('photos'))
        {
            foreach($request->file('photos') as $file)
            {
                $name = time().'_'.$file->getClientOriginalName();
                $file->storeAs('public/reviews', $name);  // stores the image in the 'public/reviews' directory

                $photo = new Photo;  // assuming you have a Photo model
                $photo->review_id = $review->id;
                $photo->filename = $name;
                $photo->save();
            }
        }

        return redirect('/');
    }

    public function index()
    {
        $reviews = Review::with('photo')->get();
        return view('reviews.index', ['reviews' => $reviews]);
    }
}
