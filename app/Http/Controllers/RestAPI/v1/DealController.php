<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Utils\Helpers;
use App\Utils\ProductManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function getFeaturedDealProducts(Request $request): JsonResponse
    {
        $user = Helpers::getCustomerInformation($request);
        $featuredDeal = FlashDeal::where(['deal_type' => 'feature_deal', 'status' => 1])
                        ->whereDate('start_date', '<=', date('Y-m-d'))
                        ->whereDate('end_date', '>=', date('Y-m-d'))
                        ->first();

        $productIDs = [];
        if ($featuredDeal) {
            $productIDs = FlashDealProduct::with(['product'])
                ->whereHas('product', function ($query) {
                    $query->active();
                })
                ->where(['flash_deal_id' => $featuredDeal->id])
                ->pluck('product_id')->toArray();
        }

        $limit  = (int) ($request['limit']  ?? 10);
        if ($limit < 1) $limit = 10;
        if ($limit > 50) $limit = 50;

        $products = Product::without(['reviews'])->with(['rating', 'tags', 'clearanceSale' => function ($query) {
                return $query->active();
            }])
            ->withCount(['reviews', 'wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->whereIn('id', $productIDs);

        $products = ProductManager::getPriorityWiseFeatureDealQuery(query: $products, dataLimit: $limit);
        
        $productFinal = Helpers::product_data_formatting($products, true);
        $productFinal = Helpers::product_payload_scrub($productFinal);
        
        return response()->json(array_values($productFinal), 200);
    }

}
