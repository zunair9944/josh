<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create roles
        $storeOwner = Role::create(['name' => 'store_owner']);
        $storeManager = Role::create(['name' => 'store_manager']);
        $staff = Role::create(['name' => 'staff']);

        // Define permissions
        $permissions = [
            // General permissions
            'view overview',
            'view bookings',
            'add bookings',
            'edit bookings',
            'view users',
            'add users',
            'edit users',
            'view patients',
            'add patients',
            'edit patients',
            'view appt types',
            'view availability',
            'view questionnaires',
            'add questionnaires',
            'edit questionnaires',
            'view settings',
            'view support',

            // Specific restrictions
            'delete patients',
            'delete bookings',
            'add/edit/delete booking types',
            'access Shopify Store Integration',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $storeOwner->givePermissionTo('view overview', 'view bookings', 'add bookings', 'edit bookings', 'view users', 'add users', 'edit users', 'view patients', 'add patients', 'edit patients', 'view appt types', 'view availability', 'view questionnaires', 'add questionnaires', 'edit questionnaires', 'view settings', 'view support');

        $storeManager->givePermissionTo('view overview', 'view bookings', 'add bookings', 'edit bookings', 'view users', 'add users', 'edit users', 'view patients', 'add patients', 'edit patients', 'view appt types', 'view availability', 'view questionnaires', 'add questionnaires', 'edit questionnaires', 'view settings', 'view support');
        $storeManager->revokePermissionTo('delete patients', 'delete questionnaires', 'access Shopify Store Integration');

        $staff->givePermissionTo('view overview', 'view bookings', 'add bookings', 'edit bookings', 'view users', 'add users', 'edit users', 'view patients', 'add patients', 'edit patients', 'view availability', 'view support');
        $staff->revokePermissionTo('delete patients', 'delete bookings', 'add/edit/delete booking types', 'add/edit/delete questionnaires', 'access Shopify Store Integration');
    }
}
