<?php

declare(strict_types=1);

namespace App\Filters\Admin\Product;

use Closure;

class FilterByGtin
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];
        if (isset($filter['gtin'])) {
            $query->where('gtin', $filter['gtin']);
        }

        return $next($request);
    }
}
