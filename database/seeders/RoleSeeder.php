<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Vytvoření oprávnění
        $permissions = [
            // Uživatelé
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            
            // Karty
            'card.view',
            'card.create',
            'card.edit',
            'card.delete',
            
            // Sety
            'set.view',
            'set.create',
            'set.edit',
            'set.delete',
            
            // Role
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            
            // Oprávnění
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',
            
            // Admin sekce
            'admin.access'
        ];

        // Vytvoření pouze chybějících oprávnění
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Vytvoření rolí a přiřazení oprávnění
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions(['card.view', 'set.view']);

        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->syncPermissions([
            'card.view', 'card.create', 'card.edit',
            'set.view', 'set.create', 'set.edit'
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'super-admin']);
        // super-admin má automaticky všechna oprávnění přes gate::before v AuthServiceProvider
    }
}