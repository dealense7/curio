<?php

declare(strict_types=1);

namespace App\Support\Testing;

use App\Models\Acl\Permission;
use App\Models\Acl\Role;
use App\Models\General\Currency;
use App\Models\General\Difficulty;
use App\Models\General\File;
use App\Models\General\Month;
use App\Models\General\PublishingStatus;
use App\Models\Tour\Tour;
use App\Models\User;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client as OauthClient;
use Laravel\Passport\Passport;
use Spatie\Permission\PermissionRegistrar;

class ProvidesTestingData
{
    /** @var array<int, Generator> */
    private static array $faker = [];

    public static function createRandomOauthClient(array $params = []): OauthClient
    {
        return OauthClient::factory()->create([
            'secret'        => null,
            'redirect_uris' => [],
            'grant_types'   => ['internal', 'internal_refresh_token', 'refresh_token'],
            ...$params,
        ]);
    }

    public static function createRandomUserAndAuthorize(array $params = [], array $options = []): User
    {
        /** @var User $user */
        $user = self::createRandomUsers($params, $options)->firstOrFail();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Passport::actingAs($user);

        return $user;
    }

    /**
     * @return Collection<int, User>
     */
    public static function createRandomUsers(array $params = [], array $options = [], int $count = 1): Collection
    {
        $users = User::factory()->count($count)->create([
            'password' => Hash::make('12345678'),
            ...$params,
        ]);

        foreach ($users as $user) {
            if (isset($options['permissions'])) {
                $permissions = self::permissionNameToModel((array) $options['permissions']);

                if ($permissions->isNotEmpty()) {
                    $user->givePermissionTo($permissions);
                }
            }

            if (isset($options['roles'])) {
                foreach ((array) $options['roles'] as $roleName) {
                    $role = Role::query()->firstOrCreate(
                        ['name' => $roleName, 'guard_name' => 'web'],
                        ['display_name' => self::getFaker()->word()],
                    );

                    $user->assignRole($role);
                }
            }
        }

        return $users;
    }

    public static function createRoleRandomItem(array $params = []): Collection
    {
        return collect([
            Role::query()->create([
                'name'         => $params['name']         ?? self::getFaker()->slug(),
                'display_name' => $params['display_name'] ?? self::getFaker()->words(2, true),
                'guard_name'   => $params['guard_name']   ?? 'web',
            ]),
        ]);
    }

    public static function createPermissionRandomItem(array $params = []): Collection
    {
        return collect([
            Permission::query()->create([
                'name'         => $params['name']         ?? 'permissions.'.self::getFaker()->slug(),
                'display_name' => $params['display_name'] ?? self::getFaker()->words(2, true),
                'guard_name'   => $params['guard_name']   ?? 'web',
            ]),
        ]);
    }

    public static function createFileRandomItem(array $params = [], int $count = 1): Collection
    {
        return File::factory()->count($count)->create($params);
    }

    public static function createTourRandomItem(array $params = [], int $count = 1): Collection
    {
        return Tour::factory()->count($count)->create($params);
    }

    public static function createDifficultyRandomItem(array $params = [], int $count = 1): Collection
    {
        return Difficulty::factory()->count($count)->create($params);
    }

    public static function createCurrencyRandomItem(array $params = [], int $count = 1): Collection
    {
        return Currency::factory()->count($count)->create($params);
    }

    public static function createPublishingStatusRandomItem(array $params = [], int $count = 1): Collection
    {
        return PublishingStatus::factory()->count($count)->create($params);
    }

    public static function createMonthRandomItem(array $params = [], int $count = 1): Collection
    {
        return Month::factory()->count($count)->create($params);
    }

    public static function getFaker(): Generator
    {
        if (! isset(self::$faker[0])) {
            self::$faker[0] = Factory::create();
        }

        return self::$faker[0];
    }

    /**
     * @param  array<int, string>  $permissions
     * @return Collection<int, Permission>
     */
    private static function permissionNameToModel(array $permissions): Collection
    {
        return collect($permissions)->map(
            static fn (string $permission): Permission => Permission::query()->firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['display_name' => $permission],
            ),
        );
    }
}
