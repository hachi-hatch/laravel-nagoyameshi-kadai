<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $category_id = $request->category_id;
        $price = $request->price;

        $sorts = [
        '   掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
        ];

        $sort_query = [];
        $sorted = "created_at desc";
        $select_sort = null;

        $query = Restaurant::query();

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
             $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('address', 'like', "%{$keyword}%")
              ->orWhereHas('categories', function ($q2) use ($keyword) {
                  $q2->where('name', 'like', "%{$keyword}%");
              });
            });
        }

        if (!empty($category_id)) {
            $query->whereHas('categories', function ($q) use ($category_id) {
            $q->where('categories.id', $category_id);
            });
        }

        if (!empty($price)) {
            $query->where('lowest_price', '<=', $price);
        }

        
        if ($request->has('select_sort')) {
            $select_sort = $request->input('select_sort');

            if ($select_sort === 'rating desc') {
                $query->ratingSortable('desc');
            } else {
                $slices = explode(' ', $select_sort);
                if (count($slices) === 2) {
                $sort_query[$slices[0]] = $slices[1];
            }
         }

        $sorted = $select_sort;
    }
        
        $restaurants = $query->paginate(15)->appends($request->all());
        $categories = Category::all();
        $total = $restaurants->total();
        
        foreach ($sort_query as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return view('restaurants.index', compact('keyword', 'category_id', 'price', 'categories', 'restaurants', 'total', 'sorts', 'sorted', 'select_sort'));
    }

    public function show($id) {
        $restaurant = Restaurant::findOrFail($id);

        return view('restaurants.show', compact('restaurant'));
    }
}
