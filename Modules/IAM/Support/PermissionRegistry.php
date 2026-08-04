<?php

namespace Modules\IAM\Support;

class PermissionRegistry
{
    /**
     * @param  array<int, string>  $availablePermissions
     * @return array<int, array{key: string, label: string, submodules: array<int, array{key: string, label: string, permissions: array<int, array{key: string, label: string, permission: string}>}>}>
     */
    public static function groups(array $availablePermissions): array
    {
        $moduleLabels = collect(config('navigation', []))
            ->mapWithKeys(function (array $group): array {
                $moduleKey = self::moduleKeyFromVisibility($group['visibility'] ?? []);

                if ($moduleKey === '') {
                    return [];
                }

                return [$moduleKey => (string) ($group['heading'] ?? str($moduleKey)->headline()->value())];
            });

        return collect($availablePermissions)
            ->groupBy(fn (string $permission): string => self::moduleKey($permission))
            ->map(function ($permissions, string $moduleKey) use ($moduleLabels): array {
                return [
                    'key' => $moduleKey,
                    'label' => $moduleLabels->get($moduleKey, self::headline($moduleKey)),
                    'submodules' => collect($permissions)
                        ->groupBy(fn (string $permission): string => self::submoduleKey($permission))
                        ->map(function ($submodulePermissions, string $submoduleKey): array {
                            return [
                                'key' => $submoduleKey,
                                'label' => self::headline($submoduleKey),
                                'permissions' => collect($submodulePermissions)
                                    ->map(fn (string $permission): array => [
                                        'key' => $permission,
                                        'label' => self::headline(self::actionKey($permission)),
                                        'permission' => $permission,
                                    ])
                                    ->sortBy('label')
                                    ->values()
                                    ->all(),
                            ];
                        })
                        ->sortBy('label')
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('label')
            ->values()
            ->all();
    }

    private static function moduleKey(string $permission): string
    {
        $resource = self::submoduleKey($permission);

        return match (true) {
            in_array($resource, ['user', 'role', 'permission', 'department', 'menu', 'system'], true) => 'admin',
            in_array($resource, ['coa'], true) => 'accounting',
            str($permission)->startsWith('item-request.') => 'item-request',
            str($permission)->startsWith('report.') => 'report',
            default => $resource,
        };
    }

    private static function submoduleKey(string $permission): string
    {
        return str($permission)->before('.')->value();
    }

    private static function actionKey(string $permission): string
    {
        return str($permission)->after('.')->replace('-', ' ')->value();
    }

    private static function headline(string $value): string
    {
        return str($value)->replace(['-', '.'], ' ')->headline()->value();
    }

    /**
     * @param  array<int, string>  $permissions
     */
    private static function moduleKeyFromVisibility(array $permissions): string
    {
        return (string) collect($permissions)
            ->map(fn (string $permission): string => self::moduleKey($permission))
            ->filter()
            ->first();
    }
}
