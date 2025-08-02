<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Restaurant;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $reservations = Reservation::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        return view('reservations.index', compact('user', 'reservations'));
    }

    public function create(Restaurant $restaurant)
    {
        return view('reservations.create', compact('restaurant'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'reservation_date'=>'required|date_format:Y-m-d',
            'reservation_time'=>'required|date_format:H:i',
            'number_of_people'=>'required|integer|between:1,50'
        ]);

        $date = $request->input('reservation_date');
        $time = $request->input('reservation_time');

        $reservation = new Reservation();
        $reservation->restaurant_id = $restaurant->id;
        $reservation->reserved_datetime = $date . ' ' . $time;
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->user_id = Auth::id();
        $reservation->save();

        return redirect()->route('restaurants.reservations.create', $restaurant)->with('flash_message', '予約が完了しました。');
    }

    public function destroy(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reservations.index')->with('error_message', '不正なアクセスです。');
        }

        $reservation->delete();

        return redirect()->route('reservations.index')->with('flash_message', '予約をキャンセルしました。');
    }
}
