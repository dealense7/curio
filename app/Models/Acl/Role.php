<?php

declare(strict_types=1);

namespace App\Models\Acl;

use App\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use HasFactory;

    public const PERMISSIONS_SCOPE = 'roles';

    protected $table = 'roles';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id'           => 'integer',
            'name'         => 'string',
            'display_name' => 'string',
            'guard_name'   => 'string',
            'created_at'   => 'datetime',
            'updated_at'   => 'datetime',
        ];
    }

    public function getId(): int
    {
        return (int) $this->getKey();
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getDisplayName(): string
    {
        return (string) $this->getAttribute('display_name');
    }

    public function getGuardName(): string
    {
        return (string) $this->getAttribute('guard_name');
    }

    public static function getPermission(string $permission): string
    {
        return static::PERMISSIONS_SCOPE.'.'.$permission;
    }

    /**
     * @param  array<int, static>  $models
     * @return Collection<int, static>
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }
}
