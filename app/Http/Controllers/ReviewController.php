<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Restaurant $restaurant) {
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
        $reviews->user_id = Auth::id();
        $reviews->save();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを投稿しました。');
    }

    public function edit(Restaurant $restaurant, Review $review) {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        return view('reviews.edit', compact('restaurant', 'review'));
    }

    public function update(Request $request, Restaurant $restaurant, Review $review) {
        $request->validate([
            'score'=>'required|integer|between:1,5',
            'content'=>'required'
        ]);

        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->save();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを編集しました。');
    }

    public function destroy(Request $request, Restaurant $restaurant, Review $review) {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $review->delete();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを削除しました。');
    }
}
