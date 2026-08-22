<?php

declare(strict_types=1);

namespace App\Filters\Admin\Retailer;

use Closure;

class FilterByScrapingEnabled
{
    public function handle(array $request, Closure $next): array
    {
        $filter = $request['filter'];
        $query  = $request['query'];

        if (isset($filter['scraping_enabled'])) {
            $query->where('scraping_enabled', $filter['scraping_enabled']);
        }

        return $next($request);
    }
}
