<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;

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

    Route::get('restaurants', [Admin\RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('restaurants/create', [Admin\RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('restaurants/store', [Admin\RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('restaurants/{restaurant}', [Admin\RestaurantController::class, 'show'])->name('restaurants.show');
    Route::get('restaurants/edit/{id}', [Admin\RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::patch('restaurants/{restaurant}', [Admin\RestaurantController::class, 'update'])->name('restaurants.update');
    Route::delete('restaurants/{restaurant}', [Admin\RestaurantController::class, 'destroy'])->name('restaurants.destroy');

    Route::get('categories', [Admin\Auth\CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories/store', [Admin\Auth\CategoryController::class, 'store'])->name('categories.store');
    Route::patch('categories/{category}', [Admin\Auth\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [Admin\Auth\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('company', [Admin\Auth\CompanyController::class, 'index'])->name('company.index');
    Route::get('company/edit', [Admin\Auth\CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('company/{company}', [Admin\Auth\CompanyController::class, 'update'])->name('company.update');

    Route::get('terms', [Admin\TermController::class, 'index'])->name('terms.index');
    Route::get('terms/edit', [Admin\TermController::class, 'edit'])->name('terms.edit');
    Route::patch('terms/{term}', [Admin\TermController::class, 'update'])->name('terms.update');
});

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::patch('user/{user}', [UserController::class, 'update'])->name('user.update');

    Route::get('restaurants/index', [RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
});

Route::group(['middleware' => 'auth'], function () {
        Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
        Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
        Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->middleware([Subscribed::class])->name('subscription.cancel');
        Route::delete('subscription', [SubscriptionController::class, 'destroy'])->middleware([Subscribed::class])->name('subscription.destroy');
        
        Route::get('subscription/create', [SubscriptionController::class, 'create'])->middleware([NotSubscribed::class])->name('subscription.create');
        Route::post('subscription', [SubscriptionController::class, 'store'])->middleware([NotSubscribed::class])->name('subscription.store');

        Route::get('restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->name('restaurants.reviews.index');
        
        Route::get('restaurants/{restaurant}/reviews/create', [ReviewController::class, 'create'])->middleware([Subscribed::class])->name('restaurants.reviews.create');
        Route::post('restaurants/{restaurant}/reviews/store', [ReviewController::class, 'store'])->middleware([Subscribed::class])->name('restaurants.reviews.store');
        Route::get('restaurants/{restaurant}/reviews/{review}/edit', [ReviewController::class, 'edit'])->middleware([Subscribed::class])->name('restaurants.reviews.edit');
        Route::patch('restaurants/{restaurant}/reviews/update/{review}', [ReviewController::class, 'update'])->middleware([Subscribed::class])->name('restaurants.reviews.update');
        Route::delete('restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'destroy'])->middleware([Subscribed::class])->name('restaurants.reviews.destroy');

        Route::get('restaurants/reservations/index', [ReservationController::class, 'index'])->middleware([Subscribed::class])->name('reservations.index');
        Route::get('restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])->middleware([Subscribed::class])->name('restaurants.reservations.create');
        Route::post('restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->middleware([Subscribed::class])->name('restaurants.reservations.store');
        Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->middleware([Subscribed::class])->name('reservations.destroy');
});