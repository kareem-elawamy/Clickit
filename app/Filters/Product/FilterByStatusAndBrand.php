<?php

namespace App\Filters\Product;

use Closure;

class FilterByStatusAndBrand
{
    public function handle($request, Closure $next)
    {
        $query = $request['query'];
        $filters = $request['filters'] ?? [];

        if (isset($filters['brand_id']) && $filters['brand_id'] != 'all') {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['product_search_type']) && $filters['product_search_type'] == 'product_gallery') {
            if (isset($filters['request_status']) && $filters['request_status'] != 'all') {
                $query->where('request_status', $filters['request_status']);
            }
        }

        if (isset($filters['is_shipping_cost_updated'])) {
            $query->where('is_shipping_cost_updated', $filters['is_shipping_cost_updated']);
        }

        if (isset($filters['code'])) {
            $query->where('code', $filters['code']);
        }

        if (isset($filters['productIds'])) {
            $query->whereIn('id', $filters['productIds']);
        }

        return $next($request);
    }
}
