<?php

declare(strict_types=1);

namespace App\Filters\Admin\Product;

use Closure;

class FilterByBrand
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];
        if (isset($filter['brand'])) {
            $query->where('brand', 'ilike', '%'.$filter['brand'].'%');
        }

        return $next($request);
    }
}
