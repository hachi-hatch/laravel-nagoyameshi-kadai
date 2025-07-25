<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Restaurant $restaurant) {
        $reviews = Review::all();

        $user = Auth::user();

        if ($user->subscribed('premium_plan')) {
            $reviews = Review::where('restaurant_id', $restaurant->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(5);
        } else {
            $reviews = Review::where('restaurant_id', $restaurant->id)
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();
        }

        return view('reviews.index', compact('restaurant','reviews', 'user'));
    }

    public function create(Restaurant $restaurant) {
        return view('reviews.create', compact('restaurant'));
    }

    public function store(Request $request, Restaurant $restaurant) {
        $request->validate([
            'score'=>'required|integer|between:1,5',
            'content'=>'required'
        ]);

        $reviews = new Review();
        $reviews->restaurant_id = $restaurant->id;
        $reviews->score = $request->input('score');
        $reviews->content = $request->input('content');       
        $reviews->user_id = Auth::user()->id;
        $reviews->save();

        return redirect()->route('reviews.index')->with('flash_message', 'レビューを投稿しました。');
    }

    public function edit(Restaurant $restaurant, Review $reviews) {
        if ($reviews->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index')->with('error_message', '不正なアクセスです。');
        }

        return view('reviews.edit', compact('restaurant', 'reviews'));
    }

    public function update(Request $request, Review $reviews) {
        $request->validate([
            'score'=>'required|integer|between:1,5',
            'content'=>'required'
        ]);

        if ($reviews->user_id !== Auth::id()) {
            return redirect()->route('reviews.index')->with('error_message', '不正なアクセスです。');
        }

        $reviews->score = $request->input('score');
        $reviews->content = $request->input('contetn');
        $reviews->save();

        return redirect()->route('reviews.index')->with('flash_message', 'レビューを編集しました。');
    }

    public function destroy(Request $request, Review $reviews) {
        if ($reviews->user_id !== Auth::id()) {
            return redirect()->route('reviews.index')->with('error_message', '不正なアクセスです。');
        }

        $reviews->delete();

        return redirect()->route('reviews.index')->with('flash_message', 'レビューを削除しました。');
    }
}
