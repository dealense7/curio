<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Enums\General\File\Disk;
use App\Enums\General\File\Status;
use App\Enums\General\File\Type;
use App\Models\Model;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model implements UuidAsPrimaryContract
{
    use SoftDeletes;

    public const string PERMISSIONS_SCOPE = 'files';

    protected $primaryKey = 'uuid';

    protected $table = 'files';

    protected $fillable = [
        'uuid',
        'extension',
        'size',
        'disk',
        'type',
        'status',
        'original_name',
        'name',
        'folder',
        'mime',
        'fileable_type',
        'fileable_id',
    ];

    protected function casts(): array
    {
        return [
            'uuid'          => 'string',
            'extension'     => 'string',
            'size'          => 'integer',
            'disk'          => Disk::class,
            'type'          => Type::class,
            'status'        => Status::class,
            'original_name' => 'string',
            'name'          => 'string',
            'folder'        => 'string',
            'mime'          => 'string',
            'fileable_id'   => 'integer',
        ];
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUuid(): string
    {
        return (string) $this->getAttribute('uuid');
    }

    public function getUuidString(): string
    {
        return $this->getUuid();
    }
}
