<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Permissions
        $permissions = [
            ['name' => 'Gestionar Usuarios', 'slug' => 'manage_users'],
            ['name' => 'Gestionar Roles', 'slug' => 'manage_roles'],
            ['name' => 'Ver Bitácora', 'slug' => 'view_audit_log'],
            ['name' => 'Gestionar Citas', 'slug' => 'manage_appointments'],
            ['name' => 'Ver Reportes', 'slug' => 'view_reports'],
            ['name' => 'Gestionar Inventario', 'slug' => 'manage_inventory'],
            ['name' => 'Consultar Stock', 'slug' => 'view_inventory'],
            ['name' => 'Gestionar Horarios', 'slug' => 'manage_schedules'],
            ['name' => 'Consultar Horarios', 'slug' => 'view_schedules'],
            ['name' => 'Gestionar Servicios', 'slug' => 'manage_services'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Define Roles and assign Permissions
        $roles = [
            'administrador' => [
                'name' => 'Administrador',
                'description' => 'Acceso total al sistema',
                'permissions' => ['manage_users', 'manage_roles', 'view_audit_log', 'manage_appointments', 'view_reports', 'manage_inventory', 'view_inventory', 'manage_schedules', 'view_schedules', 'manage_services']
            ],
            'recepcionista' => [
                'name' => 'Recepcionista',
                'description' => 'Gestión de citas y clientes',
                'permissions' => ['manage_appointments', 'manage_users', 'view_schedules', 'view_inventory'] // Limited user management
            ],
            'estilista' => [
                'name' => 'Estilista',
                'description' => 'Consulta de agenda propia',
                'permissions' => ['manage_appointments', 'view_schedules']
            ],
            'cliente' => [
                'name' => 'Cliente',
                'description' => 'Acceso a perfil y citas propias',
                'permissions' => []
            ],
        ];

        foreach ($roles as $slug => $data) {
            $role = Role::updateOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'description' => $data['description']
            ]);

            $permissionIds = Permission::whereIn('slug', $data['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        // Assign default role to existing users if any
        $adminRole = Role::where('slug', 'administrador')->first();
        User::whereNull('role_id')->update(['role_id' => $adminRole->id]);

        // Create Default Admin User
        User::updateOrCreate(
            ['email' => 'adm@adm.com'],
            [
                'password' => \Illuminate\Support\Facades\Hash::make('adm123'),
                'role_id' => $adminRole->id
            ]
        );
    }
}
