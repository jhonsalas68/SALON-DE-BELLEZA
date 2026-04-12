<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class RoleController extends Controller
{
    use LogsActivity;
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        $this->logActivity('CREATE_ROLE', "Rol definido: {$role->name}", $role->toArray());

        return redirect()->route('roles.index')->with('success', 'Rol definido exitosamente.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $oldPermissions = $role->permissions()->pluck('name')->toArray();
        $role->permissions()->sync($request->permissions ?? []);
        $newPermissions = Role::find($role->id)->permissions()->pluck('name')->toArray();

        $this->logActivity('UPDATE_ROLE', "Permisos actualizados para el rol: {$role->name}", [
            'role' => $role->name,
            'old_permissions' => $oldPermissions,
            'new_permissions' => $newPermissions
        ]);

        return redirect()->route('roles.index')->with('success', 'Permisos de rol actualizados exitosamente.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->slug, ['administrador', 'cliente'])) {
            return back()->with('error', 'No se pueden eliminar los roles base del sistema.');
        }

        $roleName = $role->name;
        $role->delete();

        $this->logActivity('DELETE_ROLE', "Rol eliminado: {$roleName}");

        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}
