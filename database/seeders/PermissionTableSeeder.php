<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolePermissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
        ];

        $userPermissions = [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
        ];
        $authorPermissions = [
            'author-list',
            'author-create',
            'author-edit',
            'author-delete',
        ];

        Role::create(['name' => 'user']);

        foreach ($rolePermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        foreach ($userPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        foreach ($authorPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
