<?php

namespace App\Filters\Product;

use Closure;

class FilterByScope
{
    public function handle($request, Closure $next)
    {
        $query = $request['query'];
        $scope = $request['scope'] ?? null;

        if ($scope == 'active') {
            $query->active();
        }

        return $next($request);
    }
}
