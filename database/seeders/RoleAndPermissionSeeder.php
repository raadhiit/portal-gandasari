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
            'item-request.view', 'item-request.create', 'item-request.update', 'item-request.update-own', 'item-request.delete', 'item-request.submit', 'item-request.comment', 'item-request.attach', 'item-request.validate', 'item-request.account', 'item-request.approve', 'item-request.reject', 'item-request.revise', 'item-request.export',
            'unit.view', 'unit.create', 'unit.update', 'unit.delete', 'unit.manage',
            'item-category.view', 'item-category.create', 'item-category.update', 'item-category.delete', 'item-category.manage',
            'coa.view', 'coa.create', 'coa.update', 'coa.delete', 'coa.manage',
            'warehouse.view', 'warehouse.create', 'warehouse.update', 'warehouse.delete', 'warehouse.manage',
            'item.view', 'item.create', 'item.update', 'item.delete', 'item.manage',
            'user.view', 'user.create', 'user.update', 'user.delete', 'user.manage',
            'department.view', 'department.create', 'department.update', 'department.delete', 'department.manage',
            'role.view', 'role.create', 'role.update', 'role.delete', 'role.manage',
            'permission.view', 'permission.create', 'permission.update', 'permission.delete', 'permission.manage',
            'menu.view', 'menu.create', 'menu.update', 'menu.delete', 'menu.manage',
            'system.view', 'system.create', 'system.update', 'system.delete', 'system.manage',
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
            'Superadmin' => $permissions,
        ];

        foreach ($roles as $name => $rolePermissions) {
            Role::findOrCreate($name, 'web')->syncPermissions($rolePermissions);
        }
    }
}
