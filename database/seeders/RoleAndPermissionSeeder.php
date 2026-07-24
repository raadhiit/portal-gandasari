<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view', 'notification.view',
            'item-request.view', 'item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.comment', 'item-request.attach', 'item-request.validate', 'item-request.account', 'item-request.approve', 'item-request.reject', 'item-request.revise', 'item-request.export',
            'unit.view', 'unit.manage', 'item-category.view', 'item-category.manage', 'coa.view', 'coa.manage', 'warehouse.view', 'warehouse.manage', 'item.view', 'item.manage',
            'user.view', 'user.manage', 'department.view', 'department.manage', 'role.view', 'role.manage', 'permission.view', 'permission.manage', 'menu.manage', 'system.manage',
            'report.request-status.view', 'report.department.view', 'report.aging.view', 'report.export',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'Admin Portal' => $permissions,
            'Requester' => ['dashboard.view', 'notification.view', 'item-request.view', 'item-request.create', 'item-request.update-own', 'item-request.submit', 'item-request.comment', 'item-request.attach'],
            'Validator' => ['dashboard.view', 'notification.view', 'item-request.view', 'item-request.comment', 'item-request.validate', 'item-request.reject', 'item-request.revise', 'unit.view', 'item-category.view', 'coa.view', 'warehouse.view', 'item.view'],
            'Accounting' => ['dashboard.view', 'notification.view', 'item-request.view', 'item-request.comment', 'item-request.account', 'item-request.reject', 'item-request.revise', 'coa.view', 'item.view'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            Role::findOrCreate($name, 'web')->syncPermissions($rolePermissions);
        }
    }
}
