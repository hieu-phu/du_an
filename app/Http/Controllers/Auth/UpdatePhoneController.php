<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UpdatePhoneController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Auth/UpdatePhone', [
            'user' => auth()->user()->only('email', 'phone', 'provider'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:15|unique:users,phone,' . auth()->id(),
        ]);

        auth()->user()->update([
            'phone' => $request->input('phone')
        ]);
        return redirect()->intended('/')
            ->with('status', 'Cập nhật số điện thoại thành công.');
    }
}
