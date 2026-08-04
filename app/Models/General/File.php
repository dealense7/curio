<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Enums\General\FileDisk;
use App\Enums\General\FileStatus;
use App\Enums\General\FileType;
use App\Models\Concerns\HasPublicId;
use App\Models\Model;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(FileFactory::class)]
class File extends Model implements UuidAsPrimaryContract
{
    use HasPublicId;
    use SoftDeletes;

    protected $table = 'files';

    /** @var list<string> */
    protected $fillable = [
        'original_name',
        'name',
        'folder',
        'extension',
        'mime',
        'size',
        'disk',
        'type',
        'status',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'size'   => 'integer',
            'disk'   => FileDisk::class,
            'type'   => FileType::class,
            'status' => FileStatus::class,
        ];
    }

    public function getOriginalName(): string
    {
        return (string) $this->getAttribute('original_name');
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getFolder(): string
    {
        return (string) $this->getAttribute('folder');
    }

    public function getExtension(): string
    {
        return (string) $this->getAttribute('extension');
    }

    public function getMime(): string
    {
        return (string) $this->getAttribute('mime');
    }

    public function getSize(): int
    {
        return (int) $this->getAttribute('size');
    }

    public function getDisk(): FileDisk
    {
        return $this->getAttribute('disk');
    }

    public function getType(): FileType
    {
        return $this->getAttribute('type');
    }

    public function getStatus(): FileStatus
    {
        return $this->getAttribute('status');
    }
}
