<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}', [Admin\UserController::class, 'show'])->name('users.show');

    Route::get('index', [Admin\RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('show/{id}', [Admin\RestaurantController::class, 'show'])->name('restaurants.show');
    Route::get('create', [Admin\RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('store', [Admin\RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('edit/{id}', [Admin\RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::patch('update', [Admin\RestaurantController::class, 'update'])->name('restaurants.update');
    Route::delete('restaurants/{restaurant}', [Admin\RestaurantController::class, 'destroy'])->name('restaurants.destroy');
});