<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Utils\BrandManager;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class BrandController extends Controller
{
    public function get_brands(Request $request)
    {
        if ($request->has('seller_id') && $request['seller_id'] != null) {
            $brand_ids = Product::active()
                ->when($request->has('seller_id') && $request['seller_id'] != null && $request['seller_id'] != 0, function ($query) use ($request) {
                    return $query->where(['added_by' => 'seller'])
                        ->where('user_id', $request['seller_id']);
                })->when($request->has('seller_id') && $request['seller_id'] != null && $request['seller_id'] == 0, function ($query) use ($request) {
                    return $query->where(['added_by' => 'admin']);
                })->pluck('brand_id');

            $brands = Brand::active()->whereIn('id', $brand_ids)->withCount('brandProducts');
        } else {
            $brands = Brand::active()->withCount('brandProducts');
        }

        $brands = self::getPriorityWiseBrandProductsQuery(query: $brands);
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        // Apply pagination directly to SQL query instead of fetching all brands into PHP memory
        $paginator = $brands->paginate($limit, ['*'], 'page', $offset);
        
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'brands' => $paginator->items()
        ];
    }

    function getPriorityWiseBrandProductsQuery($query)
    {
        $brandProductSortBy = getWebConfig(name: 'brand_list_priority');
        if ($brandProductSortBy && ($brandProductSortBy['custom_sorting_status'] == 1)) {
            if ($brandProductSortBy['sort_by'] == 'most_order') {
                return $query->withCount(['brandProducts as order_count' => function ($query) {
                    $query->join('order_details', 'products.id', '=', 'order_details.product_id');
                }])->orderByDesc('order_count');
            } elseif ($brandProductSortBy['sort_by'] == 'latest_created') {
                return $query->latest();
            } elseif ($brandProductSortBy['sort_by'] == 'first_created') {
                return $query->orderBy('id', 'asc');
            } elseif ($brandProductSortBy['sort_by'] == 'a_to_z') {
                return $query->orderBy('name', 'asc');
            } elseif ($brandProductSortBy['sort_by'] == 'z_to_a') {
                return $query->orderBy('name', 'desc');
            } else {
                return $query;
            }
        }

        return $query->latest();
    }

    public function get_products(Request $request, $brand_id)
    {
        $limit  = (int) ($request['limit']  ?? 10);
        if ($limit < 1) $limit = 10; // Failsafe for (int) 'all'
        if ($limit > 50) $limit = 50; // Cap to prevent artificial crashes

        $user = Helpers::getCustomerInformation($request);
        $products = Product::active()
            ->without(['reviews']) // CRITICAL FIX: Prevent massive N+1 global scope loading
            ->with(['clearanceSale' => function ($query) {
                return $query->active();
            }])
            ->withCount(['reviews', 'wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->where(['brand_id' => $brand_id]);

        $products = $products->paginate($limit, ['*'], 'page', request()->get('page', ($request['offset'] ?? 1)));
        $productFinal = Helpers::product_data_formatting($products->items(), true);
        
        // STRICT PAYLOAD SCRUBBING: Preserve structural JSON keys to prevent Mobile App crashes
        $productFinal = Helpers::product_payload_scrub($productFinal);

        $requestedLimit = $request['limit'] ?? 'all';
        if ($requestedLimit === 'all') {
            return response()->json(array_values($productFinal), 200);
        }

        return response()->json([
            'total_size' => $products->total(),
            'limit' => (int)$limit,
            'offset' => (int)($request['offset'] ?? 1),
            'products' => array_values($productFinal),
        ], 200);
    }
}
