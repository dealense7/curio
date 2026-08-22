<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @mixin Model
 */
trait Archivable
{
    public function archive(): bool
    {
        return $this->forceFill(['archived_at' => Carbon::now('UTC')])->save();
    }

    public function unarchive(): bool
    {
        return $this->forceFill(['archived_at' => null])->save();
    }
}
