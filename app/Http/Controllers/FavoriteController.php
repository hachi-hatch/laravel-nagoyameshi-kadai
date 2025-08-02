<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;

class FavoriteController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        
        $favorite_restaurants = $user->favorite_restaurants()
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        return view('favorites.index', compact('user', 'favorite_restaurants'));
    }

    public function store($restaurant_id)
    {
        Auth::user()->favorite_restaurants()->attach($restaurant_id);

        return back();
    }

    public function destroy($restaurant_id)
    {
        Auth::user()->favorite_restaurants()->detach($restaurant_id);

        return back();
    }
}
