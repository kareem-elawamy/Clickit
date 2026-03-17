<?php

namespace App\Filters\Product;

use Closure;

class FilterByWhere
{
    public function handle($request, Closure $next)
    {
        $query = $request['query'];
        $whereIn = $request['whereIn'] ?? [];
        $whereNotIn = $request['whereNotIn'] ?? [];

        if (count($whereIn) > 0) {
            foreach ($whereIn as $key => $whereInIndex) {
                $query->whereIn($key, $whereInIndex);
            }
        }

        if (count($whereNotIn) > 0) {
            foreach ($whereNotIn as $key => $whereNotInIndex) {
                $query->whereNotIn($key, $whereNotInIndex);
            }
        }

        return $next($request);
    }
}
