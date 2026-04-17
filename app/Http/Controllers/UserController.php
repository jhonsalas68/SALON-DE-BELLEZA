<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class UserController extends Controller
{
    use LogsActivity;
    public function index()
    {
        $users = User::with('role')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)/',
            'role_id' => 'required|exists:roles,id'
        ], [
            'password.regex' => 'La contraseña necesita mayúscula, minúscula y número.',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        $this->logActivity('CREATE', "Usuario creado: {$user->email}", $user->toArray());

        return redirect()->route('users.index')->with('success', 'Usuario registrado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'email' => "required|email|unique:users,email,{$user->id}",
            'role_id' => 'required|exists:roles,id'
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)/';
        }
        
        $request->validate($rules, [
            'password.regex' => 'La contraseña necesita mayúscula, minúscula y número.',
        ]);

        $oldData = $user->toArray();
        
        $user->update([
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $this->logActivity('UPDATE', "Usuario actualizado: {$user->email}", [
            'old' => $oldData,
            'new' => $user->toArray()
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $userData = $user->toArray();
        $email = $user->email;
        $user->delete();

        $this->logActivity('DELETE', "Usuario eliminado: {$email}", $userData);

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
