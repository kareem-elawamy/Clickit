<?php

namespace App\Filters\Product;

use Closure;
use App\Models\Translation;
use App\Models\Product;

class FilterBySearch
{
    public function handle($request, Closure $next)
    {
        $query = $request['query'];
        $searchValue = $request['searchValue'] ?? null;
        $filters = $request['filters'] ?? [];
        $repo = app(\App\Repositories\ProductRepository::class);

        if ($searchValue) {
            $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                ->where('key', 'name')
                ->where('value', 'like', "%{$searchValue}%")
                ->pluck('translationable_id')?->toArray() ?? [];

            $getProductIds = Product::where('name', 'like', "%{$searchValue}%")->pluck('id')->toArray();
            $product_ids = array_merge($product_ids, $getProductIds);

            $query->where(function($q) use ($filters, $searchValue, $repo, $product_ids) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere(function ($qcode) use ($filters) {
                        if (isset($filters['code'])) {
                            $qcode->where('code', 'like', "%{$filters['code']}%");
                        }
                    })
                    ->when(isset($filters['added_by']) && !$repo->isAddedByInHouse($filters['added_by']), function ($q_in) use ($filters, $product_ids) {
                        $q_in->when(!empty($product_ids) && count($product_ids) > 0, function ($q_ids) use ($product_ids) {
                            return $q_ids->whereIn('id', $product_ids);
                        })
                            ->where(['added_by' => 'seller'])
                            ->when(isset($filters['seller_id']), function ($q_seller) use ($filters) {
                                return $q_seller->where(['user_id' => $filters['seller_id']]);
                            });
                    })
                    ->when(isset($filters['added_by']) && $repo->isAddedByInHouse($filters['added_by']), function ($q_in) use ($filters, $product_ids) {
                        $q_in->when(!empty($product_ids) && count($product_ids) > 0, function ($q_ids) use ($product_ids) {
                            return $q_ids->orWhereIn('id', $product_ids);
                        })->where(['added_by' => 'admin']);
                    })
                    ->when(!isset($filters['added_by']), function ($q_in) use ($product_ids) {
                        $q_in->when(!empty($product_ids) && count($product_ids) > 0, function ($q_ids) use ($product_ids) {
                            return $q_ids->orWhereIn('id', $product_ids);
                        });
                    });
            });
        }

        return $next($request);
    }
}
