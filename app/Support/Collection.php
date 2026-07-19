<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @template TKey of array-key
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends EloquentCollection<TKey, TModel>
 */
class Collection extends EloquentCollection
{
    //
}
