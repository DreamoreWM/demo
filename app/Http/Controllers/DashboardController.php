<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Photospres;
use App\Models\Review;
use App\Models\SalonSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $photos = Photospres::all();
        $facebookPageUrl = SalonSetting::first()->facebook_page_url;
        $json = SalonSetting::first()->open_days;
        $openDays = json_decode($json, true);
        $isOpen = $this->isOpen();
        $categories = Category::with('prestations')->get();
        $reviews = Review::with('appointment.bookable')->with('photo')->get();
        return view('dashboard', compact('categories', 'reviews', 'isOpen', 'facebookPageUrl', 'openDays', 'photos'));
    }

    public function isOpen()
    {
        // 1. Retrieve the open_days JSON from the salon_settings table
        $salonSettings = DB::table('salon_settings')->first();
        $openDays = $salonSettings->open_days;

        // 2. Decode the JSON to a PHP array
        $openDaysArray = json_decode($openDays, true);

        // 3. Get the current day of the week and time
        $currentDay = strtolower(date('l')); // 'l' returns the full name of the day of the week
        $currentTime = date('H:i');

        // 4. Check the opening hours for the current day in the decoded array
        $todayHours = $openDaysArray[$currentDay];

        // 5. Compare the current time with the opening hours and breaks to determine if the salon is open or closed
        if ($currentTime >= $todayHours['open'] && $currentTime < $todayHours['break_start']) {
            return true;
        } elseif ($currentTime > $todayHours['break_end'] && $currentTime < $todayHours['close']) {
            return true;
        } else {
            return false;
        }
    }
}
