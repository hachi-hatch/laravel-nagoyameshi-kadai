<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        if ($request->user !== null) {
            $users = User::where('name', $request->user)->paginate(15);
            $total = $users->total();           
        } elseif ($keyword !== null) {
            $users = User::where('name', 'like', "%{$keyword}%")->paginate(15);
            $total = $users->total(); 
        } else {
            $users = User::paginate(15);
            $total = $users->total(); 
        }
        return view('admin.users.index', compact('users', 'total', 'keyword'));
    }

    public function show($id) 
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }
}
