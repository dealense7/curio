<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class Repository
{
    abstract public function getModel(): Model;

    public function getData(): Builder
    {
        return $this->getModel()->newQuery();
    }
}
