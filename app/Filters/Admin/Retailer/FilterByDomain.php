<?php

declare(strict_types=1);

namespace App\Filters\Admin\Retailer;

use Closure;

class FilterByDomain
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];

        if (isset($filter['domain'])) {
            $query->where('domain', $filter['domain']);
        }

        return $next($request);
    }
}
