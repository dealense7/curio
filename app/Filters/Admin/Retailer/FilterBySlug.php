<?php

declare(strict_types=1);

namespace App\Filters\Admin\Retailer;

use Closure;

class FilterBySlug
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];

        if (isset($filter['slug'])) {
            $query->where('slug', $filter['slug']);
        }

        return $next($request);
    }
}
