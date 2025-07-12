<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        // $keyword = $request->keyword;
        // $category_id = $request->category;
        // $price = $request->price;
        // $selected_sort = $request->select_sort;

        // $sorts = [
        //     '掲載日が新しい順' => 'created_at desc',
        //     '価格が安い順' => 'lowest_price asc'
        // ];

        // $sort_query = [];
        // $sorted = 'created_at desc';

        // if (!empty($selected_sort) && isset($sorts[$selected_sort])) {
        //     $sorted = $sorts[$selected_sort];
        // }

        // [$column, $direction] = explode(' ', $sorted);

        // $query = Restaurant::query();

        // if (!empty($keyword)) {
        //     $query->where(function ($q) use ($keyword) {
        //         $q->where('name', 'like', "%{$keyword}%")
        //         ->orWhere('address', 'like', "%{$keyword}%")
        //         ->orWhereHas('categories', function ($q2) use ($keyword) {
        //             $q2->where('name', 'like', "%{$keyword}%");
        //         });
        //     });
        // }

        // if (!empty($category_id)) {
        //     $query->whereHas('categories', function($q) use ($category_id) {
        //         $q->where('id', $category_id);
        //     });
        // }

        // if (!empty($price)) {
        //     $query->where('lowest_price', '<=', $price);
        // }

        // $query->orderBy($column, $direction);
        
        // $restaurants = $query->paginate(15)->appends($request->all()); 
        // $categories = Category::all();
        // $total = $restaurants->total();

        $keyword = $request->keyword;
        $category_id = $request->category_id;
        $price = $request->price;

        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc'
        ];

        $sort_query = [];
        $sorted = "created_at desc";

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

        foreach ($sort_query as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        $restaurants = $query->paginate(15)->appends($request->all());
        $categories = Category::all();
        $total = $restaurants->total();

        return view('restaurants.index', compact('keyword', 'category_id', 'price', 'categories', 'restaurants', 'total', 'sorts', 'sorted'));
    }
}
