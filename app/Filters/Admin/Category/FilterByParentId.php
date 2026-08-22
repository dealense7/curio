<?php

declare(strict_types=1);

namespace App\Filters\Admin\Category;

use Closure;

class FilterByParentId
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];

        if (array_key_exists('parent_id', $filter)) {
            if ($filter['parent_id'] === null) {
                $query->whereNull('parent_id');
            } else {
                $query->whereHas(
                    'parent',
                    fn ($parentQuery) => $parentQuery->where('public_id', $filter['parent_id']),
                );
            }
        }

        return $next($request);
    }
}
