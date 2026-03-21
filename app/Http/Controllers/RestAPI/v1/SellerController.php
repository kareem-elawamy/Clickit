<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
use App\Models\Shop;
use App\Traits\InHouseTrait;
use App\Utils\Helpers;
use App\Utils\ProductManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;

class SellerController extends Controller
{
    use InHouseTrait;

    public function __construct(
        private Seller       $seller,
    )
    {
    }

    public function get_seller_info(Request $request)
    {
        $data = [];
        $sellerId = $request['seller_id'];
        $seller = $sellerId != 0 ? Seller::with(['shop'])->where(['id' => $request['seller_id']])->first(['id', 'f_name', 'l_name', 'phone', 'image', 'minimum_order_amount']) : null;

        $productIds = Product::active()
            ->when($sellerId == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($sellerId != 0, function ($query) use ($sellerId) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $sellerId);
            })
            ->withCount('reviews')
            ->pluck('id')->toArray();

        $avgRating = Review::active()->whereIn('product_id', $productIds)->avg('rating');
        $totalReview = Review::active()->whereIn('product_id', $productIds)->count();
        $totalOrder = Review::active()->whereIn('product_id', $productIds)->groupBy('order_id')->count();
        $totalProduct = Product::active()
            ->when($sellerId == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($sellerId != 0, function ($query) use ($sellerId) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $sellerId);
            })->count();

        $minimumOrderAmount = 0;
        $minimumOrderAmountStatus = getWebConfig(name: 'minimum_order_amount_status');
        $minimumOrderAmountBySeller = getWebConfig(name: 'minimum_order_amount_by_seller');
        $ratingPercentage = round(($avgRating * 100) / 5);
        if ($sellerId != 0 && $minimumOrderAmountStatus && $minimumOrderAmountBySeller) {
            $minimumOrderAmount = $seller['minimum_order_amount'];
            unset($seller['minimum_order_amount']);
        }

        $data['seller'] = $seller;
        $data['avg_rating'] = (float)$avgRating;
        $data['positive_review'] = round(($avgRating * 100) / 5);
        $data['total_review'] = $totalReview;
        $data['total_order'] = $totalOrder;
        $data['total_product'] = $totalProduct;
        $data['minimum_order_amount'] = $minimumOrderAmount;
        $data['rating_percentage'] = $ratingPercentage;

        return response()->json($data, 200);
    }

    public function getVendorProducts($seller_id, Request $request): JsonResponse
    {
        $requestedLimit = $request['limit'] ?? 'all';
        $limit  = (int) ($request['limit']  ?? 10);
        if ($limit < 1) $limit = 10;
        if ($limit > 50) $limit = 50;
        $request->merge(['limit' => $limit]);

        $products = ProductManager::get_seller_products($seller_id, $request);
        
        $productsList = $products->total() > 0 ? Helpers::product_data_formatting($products->items(), true) : [];
        $productsList = Helpers::product_payload_scrub($productsList);

        if ($requestedLimit === 'all') {
            return response()->json(array_values($productsList), 200);
        }

        return response()->json([
            'total_size' => $products->total(),
            'limit' => (int)$limit,
            'offset' => (int)$request['offset'],
            'products' => array_values($productsList)
        ]);
    }

    public function getSellerList(Request $request, $type)
    {
        $sellers = $this->seller->when($type == 'top', function($query){
                return $query->whereHas('orders');
            })
            ->approved()->with(['shop', 'orders', 'product.reviews' => function ($query) {
                $query->active();
            }])
            ->withCount(['orders', 'product' => function ($query) {
                $query->active();
            }])
            ->get()
            ->each(function ($seller) {
                $seller['temporary_close'] = (int)$seller?->shop?->temporary_close ?? 0;
                $seller->product?->map(function ($product) {
                    $product['rating'] = $product?->reviews?->where('status', 1)->pluck('rating')->sum();
                    $product['rating_count'] = $product->reviews?->where('status', 1)->count();
                    $product['rating_count'] = $product->reviews?->where('status', 1)->count();
                });
                $seller['total_rating'] = $seller?->product->pluck('rating')->sum();
                $seller['rating_count'] = $seller->product->pluck('rating_count')->sum();
                $seller['review_count'] = $seller->product->pluck('rating_count')->sum();
                $seller['average_rating'] = $seller['total_rating'] / ($seller['rating_count'] == 0 ? 1 : $seller['rating_count']);
                $seller->is_vacation_mode_now = checkVendorAbility(type: 'vendor', status: 'vacation_status', vendor: $seller?->shop);

                unset($seller['product']);
                unset($seller['orders']);
        });

        $inhouseProducts = Product::active()->with(['reviews', 'rating'])
        ->withCount(['reviews' => function ($query) {
            $query->active();
        }])
        ->where(['added_by' => 'admin'])->get();
        $inhouseProductCount = $inhouseProducts->count();

        $inhouseReviewData = Review::active()->whereIn('product_id', $inhouseProducts->pluck('id'));
        $inhouseReviewDataCount = $inhouseReviewData->count();
        $inhouseRattingStatusPositive = (clone $inhouseReviewData)->where('rating', '>=', 4)->count();

        $inhouseShop = $this->getInHouseShopObject();
        $inhouseShop->id = 0;

        $inhouseSeller = $this->getInHouseSellerObject();
        $inhouseSeller->id = 0;
        $inhouseSeller->total_rating = $inhouseReviewDataCount;
        $inhouseSeller->rating_count = $inhouseReviewDataCount;
        $inhouseSeller->review_count = $inhouseReviewDataCount;
        $inhouseSeller->product_count = $inhouseProductCount;
        $inhouseSeller->average_rating = $inhouseReviewData->avg('rating');
        $inhouseSeller->positive_review = $inhouseReviewDataCount != 0 ? ($inhouseRattingStatusPositive * 100) / $inhouseReviewDataCount : 0;
        $inhouseSeller->orders_count = Order::where(['seller_is' => 'admin'])->count();
        $inhouseSeller->temporary_close = (int)$inhouseShop->temporary_close ?? 0;
        $inhouseSeller->shop = $inhouseShop;
        $sellers->prepend($inhouseSeller);

        if ($type == 'top') {
            $sellers = ProductManager::getPriorityWiseTopVendorQuery(query: $sellers);
        } elseif ($type == 'new') {
            $sellers = $sellers->sortByDesc('id');
        } else {
            $sellers = ProductManager::getPriorityWiseVendorQuery(query: $sellers);
        }

        $currentPage = $request['offset'] ?? Paginator::resolveCurrentPage('page');
        $totalSize = $sellers->count();
        $sellers = $sellers->forPage($currentPage, $request->get('limit', DEFAULT_DATA_LIMIT));

        $sellers = new LengthAwarePaginator($sellers, $totalSize, $request->get('limit', DEFAULT_DATA_LIMIT), $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'appends' => $request->all(),
        ]);

        return [
            'total_size' => $sellers->total(),
            'limit' => (int)$request['limit'],
            'offset' => (int)$request['offset'],
            'sellers' => $sellers->values()
        ];

    }

    public function more_sellers(Request $request)
    {
        $limit = $request->get('limit', 15);
        $topVendorsList = Shop::active()
            ->whereHas('seller', function($query){
                return $query->whereHas('orders');
            })
            ->with(['seller' => function ($query) {
                $query->withCount(['orders']);
            }])
            ->orderByDesc(\App\Models\Order::selectRaw('count(*)')
                ->whereColumn('seller_id', 'shops.seller_id')
                ->where('seller_is', 'seller')
            )
            ->take($limit)
            ->get();

        return array_values($topVendorsList->toArray());
    }

    public function get_seller_best_selling_products($seller_id, Request $request)
    {
        $requestedLimit = $request['limit'] ?? 'all';
        $limit  = (int) ($request['limit']  ?? 10);
        if ($limit < 1) $limit = 10;
        if ($limit > 50) $limit = 50;

        $products = ProductManager::get_seller_best_selling_products($request, $seller_id, $limit, $request['offset']);
        $productsList = isset($products['products'][0]) ? Helpers::product_data_formatting($products['products'], true) : [];
        $productsList = Helpers::product_payload_scrub($productsList);

        if ($requestedLimit === 'all') {
            return response()->json(array_values($productsList), 200);
        }

        $products['products'] = array_values($productsList);
        $products['limit'] = (int)$limit;
        return response()->json($products, 200);
    }

    public function get_sellers_featured_product($seller_id, Request $request)
    {
        $requestedLimit = $request['limit'] ?? 'all';
        $limit  = (int) ($request['limit']  ?? 10);
        if ($limit < 1) $limit = 10;
        if ($limit > 50) $limit = 50;

        $user = Helpers::getCustomerInformation($request);
        $featuredProducts = Product::active()->without(['reviews'])->with(['rating', 'clearanceSale' => function ($query) {
                return $query->active();
            }])
            ->withCount(['reviews'])
            ->withCount(['wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->where(['featured' => '1'])
            ->when($seller_id == '0', function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($seller_id != '0', function ($query) use ($seller_id) {
                return $query->where(['added_by' => 'seller', 'user_id' => $seller_id]);
            });

        $featuredProductsList = ProductManager::getPriorityWiseFeaturedProductsQuery(query: $featuredProducts, dataLimit: $limit, offset: $request['offset']);
        $featuredProductsList?->map(function ($product) {
            $product['reviews_count'] = $product->reviews_count;
            $product['rating'] = isset($product?->rating[0]) ? $product->rating[0] : null;
        });
        
        $productsList = $featuredProductsList->items() ? Helpers::product_data_formatting($featuredProductsList->items(), true) : [];
        $productsList = Helpers::product_payload_scrub($productsList);

        if ($requestedLimit === 'all') {
            return response()->json(array_values($productsList), 200);
        }

        return response()->json([
            'total_size' => $featuredProductsList->total(),
            'limit' => (int)$limit,
            'offset' => (int)($request['offset'] ?? 1),
            'products' => array_values($productsList)
        ], 200);
    }

    public function get_sellers_recommended_products($seller_id, Request $request)
    {
        $requestedLimit = $request['limit'] ?? 'all';
        $limit  = (int) ($request['limit']  ?? 10);
        if ($limit < 1) $limit = 10;
        if ($limit > 50) $limit = 50;

        $products = Product::active()->without(['reviews'])->with(['category'])
                    ->when($seller_id == '0', function ($query){
                        return $query->where(['added_by' => 'admin']);
                    })
                    ->when($seller_id != '0', function ($query) use ($seller_id) {
                        return $query->where(['added_by' => 'seller', 'user_id'=>$seller_id]);
                    })
                    ->withCount('orderDelivered')
                    ->withCount('reviews')
                    ->withSum('tags', 'visit_count')
                    ->orderBy('order_delivered_count', 'desc')
                    ->orderBy('tags_sum_visit_count', 'desc')
                    ->paginate($limit, ['*'], 'page', $request['offset'] ?? 1);

        $products?->map(function ($product) {
            $product['reviews_count'] = $product->reviews_count;
            $product['rating'] = isset($product?->rating[0]) ?$product->rating[0] : null;
        });

        $productsList = $products ? Helpers::product_data_formatting($products, true) : [];
        $productsList = Helpers::product_payload_scrub($productsList);

        if ($requestedLimit === 'all') {
            return response()->json(array_values($productsList), 200);
        }

        return response()->json([
            'total_size' => $products->total(),
            'limit' => (int)$limit,
            'offset' => (int)($request['offset'] ?? 1),
            'products' => array_values($productsList)
        ], 200);
    }
}
