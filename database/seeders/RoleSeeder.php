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
            'admin.access',

            // Registry
            'register.view',
            'register.create',
            'register.edit',
            'register.delete',
            
            // --- Nová oprávnění pro Sbírky ---
            'collections.view',
            'collections.create',
            'collections.edit.own',
            'collections.delete.own',
            'collections.edit.any',
            'collections.delete.any',
            // ----------------------------------
        ];

        // Vytvoření pouze chybějících oprávnění
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Vytvoření rolí a přiřazení oprávnění
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([
            'card.view', 
            'set.view',
            'collections.view', 
            'collections.create', 
            'collections.edit.own', 
            'collections.delete.own'
        ]);

        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorRole->syncPermissions([
            'card.view', 'card.create', 'card.edit',
            'set.view', 'set.create', 'set.edit'
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->pluck('name')->toArray());

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        // super-admin má automaticky všechna oprávnění přes gate::before v AuthServiceProvider
    }
}