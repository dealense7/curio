<?php

declare(strict_types=1);

namespace App\Filters\Admin;

use Closure;

class FilterByIsActive
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];

        if (array_key_exists('is_active', $filter)) {
            $query->where('is_active', $filter['is_active']);
        }

        return $next($request);
    }
}
