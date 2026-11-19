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
        \App\Support\CancelledPasses::remove([$booking], 'Booking list');
        return redirect()->back()->withSuccess('Booking has been deleted successfully!');
    }

    /** Delete several registrations at once. */
    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Tick the registrations you want to delete first.',
        ]);

        $deleted = \App\Support\CancelledPasses::remove(Booking::whereIn('id', $data['ids'])->get(), 'Booking list – bulk delete');

        return back()->with('success', $deleted . ' registration(s) deleted.');
    }
}
