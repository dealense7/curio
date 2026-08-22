<?php

declare(strict_types=1);

namespace App\Filters\Admin\Retailer;

use Closure;

class FilterByName
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];

        if (isset($filter['name'])) {
            $query->where('name', 'ilike', '%'.$filter['name'].'%');
        }

        return $next($request);
    }
}
