<?php

declare(strict_types=1);

namespace App\Filters\Admin\Product;

use Closure;

class FilterByCategoryId
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];
        if (array_key_exists('category_id', $filter)) {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('public_id', $filter['category_id']));
        }

        return $next($request);
    }
}
