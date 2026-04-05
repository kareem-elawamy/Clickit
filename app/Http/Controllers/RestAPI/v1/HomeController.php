<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestAPI\v1\CategoryThinResource;
use App\Http\Resources\RestAPI\v1\ProductThinResource;
use App\Http\Resources\RestAPI\v1\SellerThinResource;
use App\Http\Resources\RestAPI\v1\BannerResource;
use App\Http\Resources\RestAPI\v1\BrandResource;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // ─── Unified Endpoint with Optimized Payload ──────────────────────────────
    public function getHomeData(Request $request): JsonResponse
    {
        $guestId  = $request->get('guest_id') ?? '0';
        $cacheKey = 'api_v1_home_data_optimized_' . app()->getLocale() . '_guest_' . $guestId;

        $data = Cache::remember($cacheKey, 60 * 60, function () use ($guestId) {
            return [
                'banners'                 => BannerResource::collection($this->getBanners()),
                'categories'              => CategoryThinResource::collection($this->getTopCategories()),
                'home_categories'         => $this->getHomeCategories(),
                'flash_deals'             => $this->getActiveFlashDeal($guestId),
                'latest_products'         => $this->getLatestProducts($guestId),
                'featured_deals'          => $this->getActiveFeaturedDeal($guestId),
                'top_sellers'             => SellerThinResource::collection($this->getTopSellers()),
                'brands'                  => $this->getBrands(),
                'best_selling_products'   => ProductThinResource::collection($this->getBestSellingProducts($guestId)),
                'featured_products'       => ProductThinResource::collection($this->getFeaturedProducts($guestId)),
                'find_what_you_need'      => [],
                'just_for_you'            => [],
                'recommended_products'    => [],
            ];
        });

        return response()->json($data, 200);
    }

    public function getEssentialData(Request $request): JsonResponse
    {
        $guestId  = $request->get('guest_id') ?? '0';
        $cacheKey = 'api_v1_home_essential_' . app()->getLocale() . '_guest_' . $guestId;

        $data = Cache::remember($cacheKey, 60 * 60, function () use ($guestId) {
            $flashDeals = $this->getActiveFlashDeal($guestId);
            return [
                'banners'            => BannerResource::collection($this->getBanners()),
                'categories'         => CategoryThinResource::collection($this->getTopCategories()),
                'flash_deals'        => $flashDeals ? $flashDeals : (object)[],
                'find_what_you_need' => [],
            ];
        });

        return response()->json($data, 200);
    }

    public function getDiscoveryData(Request $request): JsonResponse
    {
        $guestId  = $request->get('guest_id') ?? '0';
        $cacheKey = 'api_v1_home_discovery_' . app()->getLocale() . '_guest_' . $guestId;

        $data = Cache::remember($cacheKey, 60 * 60, function () use ($guestId) {
            $featuredDeals = $this->getActiveFeaturedDeal($guestId);
            return [
                'top_sellers'    => SellerThinResource::collection($this->getTopSellers()),
                'brands'         => $this->getBrands(),
                'featured_deals' => $featuredDeals ? $featuredDeals : (object)[],
            ];
        });

        return response()->json($data, 200);
    }

    public function getProductsData(Request $request): JsonResponse
    {
        $guestId  = $request->get('guest_id') ?? '0';
        $cacheKey = 'api_v1_home_products_' . app()->getLocale() . '_guest_' . $guestId;

        $data = Cache::remember($cacheKey, 60 * 60, function () use ($guestId) {
            return [
                'home_categories'       => $this->getHomeCategories(),
                'latest_products'       => $this->getLatestProducts($guestId),
                'best_selling_products' => ProductThinResource::collection($this->getBestSellingProducts($guestId)),
                'featured_products'     => ProductThinResource::collection($this->getFeaturedProducts($guestId)),
                'just_for_you'          => [],
                'recommended_products'  => [],
            ];
        });

        return response()->json($data, 200);
    }

    // ─── Data Providers ────────────────────────────────────────────────────────

    private function getBanners()
    {
        return Banner::where('published', 1)
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'banner_type', 'photo', 'url', 'resource_type', 'resource_id']);
    }

    private function getTopCategories()
    {
        return Category::where('position', 0)
            ->orderByDesc('priority')
            ->limit(20)
            ->get(['id', 'name', 'slug', 'icon']);
    }

    private function getActiveFlashDeal($guestId = '0')
    {
        $now = now();
        $deal = FlashDeal::where('status', 1)
            ->where('deal_type', 'flash_deal')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->select(['id', 'title', 'start_date', 'end_date', 'background_color', 'text_color'])
            ->first();

        if (!$deal)
            return (object)[];

        $productIds = FlashDealProduct::where('flash_deal_id', $deal->id)->pluck('product_id');

        $products = Product::active()
            ->whereIn('id', $productIds)
            ->select(['id', 'name', 'slug', 'thumbnail', 'unit_price', 'purchase_price', 'discount', 'discount_type', 'user_id'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->limit(8)
            ->get();

        return [
            'id'       => $deal->id,
            'title'    => $deal->title,
            'end_date' => $deal->end_date,
            'products' => ProductThinResource::collection($products)
        ];
    }

    private function getActiveFeaturedDeal($guestId = '0')
    {
        $now = now();
        $deal = FlashDeal::where('status', 1)
            ->where('deal_type', 'feature_deal')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->select(['id', 'title', 'start_date', 'end_date', 'background_color', 'text_color'])
            ->first();

        if (!$deal)
            return (object)[];

        $productIds = FlashDealProduct::where('flash_deal_id', $deal->id)->pluck('product_id');

        $products = Product::active()
            ->whereIn('id', $productIds)
            ->select(['id', 'name', 'slug', 'thumbnail', 'unit_price', 'purchase_price', 'discount', 'discount_type', 'user_id'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->limit(15)
            ->get();

        return [
            'id'       => $deal->id,
            'title'    => $deal->title,
            'end_date' => $deal->end_date,
            'products' => ProductThinResource::collection($products)
        ];
    }

    private function getTopSellers()
    {
        $shops = Shop::active()
            ->select(['id', 'seller_id', 'name', 'image', 'image_storage_type'])
            ->with(['seller' => function($q) {
                $q->select(['id', 'f_name', 'l_name']);
            }])
            ->withCount(['products' => fn($q) => $q->active()])
            ->orderByDesc('products_count')
            ->limit(15)
            ->get();

        return $shops->map(function ($shop) {
            $seller = $shop->seller;
            if ($seller) {
                // Attach shop to avoid N+1 queries in resource mapping
                $seller->setRelation('shop', $shop);
                return $seller;
            }
            return null;
        })->filter();
    }

    private function getLatestProducts($guestId = '0')
    {
        $query = Product::active()
            ->select(['id', 'name', 'slug', 'thumbnail', 'unit_price', 'purchase_price', 'discount', 'discount_type', 'user_id'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        $totalSize = $query->count();
        $products = $query->orderByDesc('id')
            ->limit(20)
            ->get();

        return [
            'total_size' => $totalSize,
            'products'   => ProductThinResource::collection($products)
        ];
    }

    private function getHomeCategories(): array
    {
        $homeCategories = Category::where('home_status', true)
            ->orderByDesc('priority')
            ->get(['id', 'name', 'slug', 'icon']);
        
        $categoriesResponse = [];

        foreach ($homeCategories as $category) {
            $products = Product::active()
                ->where('category_id', $category->id)
                ->select(['id', 'name', 'slug', 'thumbnail', 'unit_price', 'purchase_price', 'discount', 'discount_type', 'user_id'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->limit(5)
                ->get();

            if ($products->count() > 0) {
                $categoriesResponse[] = [
                    'id'       => $category->id,
                    'name'     => $category->name,
                    'products' => ProductThinResource::collection($products)
                ];
            }
        }

        return $categoriesResponse;
    }

    private function getFeaturedProducts($guestId = '0')
    {
        return Product::active()
            ->where('featured', 1)
            ->select(['id', 'name', 'slug', 'thumbnail', 'unit_price', 'purchase_price', 'discount', 'discount_type', 'user_id'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('id')
            ->limit(15)
            ->get();
    }

    private function getBestSellingProducts($guestId = '0')
    {
        $getOrderedProductIds = \App\Models\OrderDetail::whereHas('product', function ($q) {
                $q->active();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderByDesc('count')
            ->limit(15)
            ->pluck('product_id')
            ->toArray();

        $products = Product::active()
            ->whereIn('id', $getOrderedProductIds)
            ->select(['id', 'name', 'slug', 'thumbnail', 'unit_price', 'purchase_price', 'discount', 'discount_type', 'user_id'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get();

        return $products->sortBy(function($model) use ($getOrderedProductIds){
            return array_search($model->id, $getOrderedProductIds);
        })->values();
    }

    private function getBrands()
    {
        $brands = Brand::where('status', 1)
            ->orderByDesc('id')
            ->limit(15)
            ->get(['id', 'name', 'image']);

        return BrandResource::collection($brands);
    }
}
