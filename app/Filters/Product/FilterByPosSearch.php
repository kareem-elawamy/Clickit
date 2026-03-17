<?php

namespace App\Filters\Product;

use Closure;

class FilterByPosSearch
{
    public function handle($request, Closure $next)
    {
        $query = $request['query'];
        $filters = $request['filters'] ?? [];

        if (isset($filters['search_from']) && $filters['search_from'] == 'pos') {
            $searchKeyword = str_ireplace(['\'', '"', ',', ';', '<', '>', '?'], ' ', preg_replace('/\s\s+/', ' ', $filters['keywords']));
            $query->where(function ($q) use ($filters) {
                $q->where('code', 'like', "%{$filters['keywords']}%")
                    ->orWhere('name', 'like', "%{$filters['keywords']}%");
            })
                ->orderByRaw("CASE WHEN name LIKE '%{$searchKeyword}%' THEN 1 ELSE 2 END, LOCATE('{$searchKeyword}', name), name");
        }

        return $next($request);
    }
}
