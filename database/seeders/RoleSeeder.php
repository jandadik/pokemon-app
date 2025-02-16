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
            
            // Admin sekce
            'admin.access'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Vytvoření rolí a přiřazení oprávnění
        Role::create(['name' => 'user'])
            ->givePermissionTo(['card.view', 'set.view']);

        Role::create(['name' => 'editor'])
            ->givePermissionTo([
                'card.view', 'card.create', 'card.edit',
                'set.view', 'set.create', 'set.edit'
            ]);

        Role::create(['name' => 'admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'super-admin']);
        // super-admin má automaticky všechna oprávnění přes gate::before v AuthServiceProvider
    }
}