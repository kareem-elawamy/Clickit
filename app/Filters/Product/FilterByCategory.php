<?php

namespace App\Filters\Product;

use Closure;

class FilterByCategory
{
    public function handle($request, Closure $next)
    {
        $query = $request['query'];
        $filters = $request['filters'] ?? [];

        if (isset($filters['category_id']) && !empty($filters['category_id']) && $filters['category_id'] != 'all') {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['sub_category_id']) && !empty($filters['sub_category_id']) && $filters['sub_category_id'] != 'all') {
            $query->where('sub_category_id', $filters['sub_category_id']);
        }

        if (isset($filters['sub_sub_category_id']) && !empty($filters['sub_sub_category_id']) && $filters['sub_sub_category_id'] != 'all') {
            $query->where('sub_sub_category_id', $filters['sub_sub_category_id']);
        }

        return $next($request);
    }
}
