<?php

namespace App\Filters\Product;

use Closure;

class FilterByAddedBy
{
    public function handle($request, Closure $next)
    {
        $query = $request['query'];
        $filters = $request['filters'] ?? [];
        $repo = app(\App\Repositories\ProductRepository::class);

        if (isset($filters['added_by'])) {
            if ($repo->isAddedByInHouse($filters['added_by'])) {
                $query->where(['added_by' => 'admin']);
            } else {
                $query->where(['added_by' => 'seller'])
                    ->when(isset($filters['request_status']) && $filters['request_status'] != 'all', function ($q) use ($filters) {
                        $q->where(['request_status' => $filters['request_status']]);
                    })
                    ->when(isset($filters['seller_id']) && $filters['seller_id'] != 'all', function ($q) use ($filters) {
                        $q->where(['user_id' => $filters['seller_id']]);
                    });
            }
        }

        return $next($request);
    }
}
