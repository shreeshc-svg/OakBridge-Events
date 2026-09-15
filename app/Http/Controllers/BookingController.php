<?php

namespace App\Http\Controllers;
use App\Models\Booking;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::latest()->get();
        return view('backend.booking.index',compact('bookings'));
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->back()->withSuccess('Booking has been deleted successfully!');
    }
}