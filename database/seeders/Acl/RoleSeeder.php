<?php

declare(strict_types=1);

namespace Database\Seeders\Acl;

use App\Enums\Acl\DefaultRoles;
use App\Models\Acl\Permission;
use App\Models\Acl\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $now = Carbon::now('UTC');

        Role::query()->upsert(
            [[
                'name'         => DefaultRoles::ADMINISTRATOR->value,
                'guard_name'   => 'web',
                'display_name' => 'Administrator',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]],
            ['name', 'guard_name'],
            ['display_name', 'updated_at'],
        );

        $administratorRole = Role::query()->firstWhere('name', DefaultRoles::ADMINISTRATOR->value);

        if ($administratorRole === null) {
            return;
        }

        $administratorRole->syncPermissions(Permission::query()->pluck('name')->all());
    }
}
