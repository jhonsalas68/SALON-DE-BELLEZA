<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'La contraseña actual no es correcta.');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'UPDATE',
            'description' => 'Contraseña actualizada por el usuario'
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
