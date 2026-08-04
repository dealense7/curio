<?php

declare(strict_types=1);

namespace App\Http\Resources\General;

use App\Models\General\File;
use App\Support\Resources\JsonResource;

class FileResource extends JsonResource
{
    protected static array $transformMapping = [
        'original_name' => 'original_name',
        'name'          => 'name',
        'folder'        => 'folder',
        'extension'     => 'extension',
        'mime'          => 'mime',
        'size'          => 'size',
        'disk'          => 'disk',
        'type'          => 'type',
        'status'        => 'status',
        'created_at'    => 'created_at',
        'updated_at'    => 'updated_at',
    ];

    public function __construct(?File $resource)
    {
        $this->resource = $resource;
    }
}
