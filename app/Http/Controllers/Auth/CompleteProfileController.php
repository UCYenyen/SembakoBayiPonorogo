<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompleteProfileController extends Controller
{
    public function show()
    {
        return view('auth.complete-profile');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^[0-9\s]{10,15}$/'],
        ]);

        $phoneNumber = '+62' . str_replace(' ', '', $request->phone_number);

        /** @var User $user */
       $user = Auth::user();
        $user->update([
            'phone_number' => $phoneNumber,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile completed successfully!');
    }
}
