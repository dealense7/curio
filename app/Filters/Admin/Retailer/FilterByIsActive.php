<?php

declare(strict_types=1);

namespace App\Filters\Admin\Retailer;

use Closure;

class FilterByIsActive
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];

        if (isset($filter['is_active'])) {
            $query->where('is_active', $filter['is_active']);
        }

        return $next($request);
    }
}
