<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function create() {
        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    public function store(Request $request) {
        $user = Auth::user()->newSubscription('premium_plan', 'price_1RjxUjRXD0aIuuSQuSf2QBDW')
        ->create($request->paymentMethodId);

        return redirect()->route('user.index')->with('flash_message', '有料プランへの登録が完了しました。');
    }

    public function edit() {
        $user = Auth::user();
        $intent = $user->createSetupIntent();

        return view('subscription.edit', compact('user', 'intent'));
    }

    public function update(Request $request) {
        $intent = Auth::user()->updateDefaultPaymentMethod($request->paymentMethodId);

        return redirect()->route('user.index')->with('flash_message', 'お支払い方法を変更しました。');
    }

    public function cancel() { 
        return view('subscription.cancel');
    }

    public function destroy() {
        $user = Auth::user();
        $intent = $user->subscription('premium_plan')->cancel();

        return redirect()->route('user.index')->with('flash_message', '有料プランを解約しました。');
    }
}
