<?php

declare(strict_types=1);

namespace Database\Seeders\Acl;

use App\Models\Acl\Permission;
use App\Models\General\Country\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * @var array<string, array{display_name: string, permissions: array<string, string>}>
     */
    protected array $groupedPermissions = [
        Country::PERMISSIONS_SCOPE => [
            'display_name' => 'Country management',
            'permissions'  => [
                'read'   => 'View countries',
                'create' => 'Create countries',
                'update' => 'Update countries',
                'delete' => 'Delete countries',
            ],
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $now = Carbon::now('UTC');
        $rows = [];

        foreach ($this->groupedPermissions as $scope => $values) {
            foreach ($values['permissions'] as $permissionKey => $displayName) {
                $rows[] = [
                    'name' => $scope.'.'.$permissionKey,
                    'guard_name' => 'web',
                    'display_name' => $displayName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        Permission::query()->upsert($rows, ['name', 'guard_name'], ['display_name', 'updated_at']);
    }
}
