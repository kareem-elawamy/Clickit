<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Utils\CategoryManager;
use App\Utils\Helpers;
use App\Http\Resources\RestAPI\v1\CategoryThinResource;
use App\Http\Resources\RestAPI\v1\ProductCategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get_categories(Request $request): JsonResponse
    {
        $sellerId = $request->seller_id ?? null;
        $cacheKey = 'api_v1_categories_seller_' . ($sellerId ?? 'all') . '_lang_' . app()->getLocale();

        $categories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60*60*4, function () use ($sellerId) {
            $categoriesID = [];
            if ($sellerId != null) {
                $categoriesID = Product::active()
                    ->when($sellerId != null && $sellerId != 0, function ($query) use ($sellerId) {
                        return $query->where(['added_by' => 'seller', 'user_id' => $sellerId]);
                    })->when($sellerId != null && $sellerId == 0, function ($query) {
                        return $query->where(['added_by' => 'admin']);
                    })
                    ->distinct()
                    ->pluck('category_id');
            }

            $categories = Category::when($sellerId != null, function ($query) use ($categoriesID) {
                return $query->whereIn('id', $categoriesID);
            })
                ->select(['id', 'name', 'slug', 'icon', 'parent_id', 'position'])
                ->with(['childes' => function ($query) {
                    return $query->select(['id', 'name', 'slug', 'icon', 'parent_id', 'position'])
                                 ->where('position', 1);
                }])
                ->where('position', 0)
                ->orderByDesc('priority')
                ->get();

            // Transform categories to array and enforce Image Fallback recursively
            $formatCategory = function($cat) use (&$formatCategory) {
                // Handle both initial Eloquent Models and recursively casted sub-arrays safely.
                $catData = is_array($cat) ? $cat : (method_exists($cat, 'toArray') ? $cat->toArray() : (array) $cat);
                
                $icon = $catData['icon'] ?? '';
                $isDefault = ($icon === '' || $icon === 'def.png' || $icon === 'null');
                
                // Return only essential data
                $formatted = [
                    'id'        => $catData['id'] ?? 0,
                    'name'      => $catData['name'] ?? '',
                    'slug'      => $catData['slug'] ?? '',
                    'parent_id' => $catData['parent_id'] ?? 0,
                    'position'  => $catData['position'] ?? 0,
                    'icon'      => $isDefault
                                    ? null
                                    : asset('storage/app/public/category/' . $icon),
                ];

                if (!empty($catData['childes'])) {
                    $formatted['childes'] = array_map(function($child) use ($formatCategory) {
                        return $formatCategory($child);
                    }, $catData['childes']);
                }

                return $formatted;
            };

            return $categories->map(function($cat) use ($formatCategory) {
                return $formatCategory($cat);
            })->values();
        });

        return response()->json($categories);
    }

    public function get_childes(Request $request, $id): JsonResponse
    {
        $childes = Category::where('parent_id', $id)
            ->select(['id', 'name', 'slug', 'icon', 'parent_id', 'position'])
            ->orderByDesc('priority')
            ->get();

        $formatted = CategoryThinResource::collection($childes);

        return response()->json($formatted, 200);
    }

    public function get_products(Request $request, $id): JsonResponse
    {
        // CRITICAL FIX: Never allow 'all' — enforce hard-default pagination to prevent 122MB payloads
        $limit  = (int) ($request['limit']  ?? 10);
        if ($limit < 1) {
            $limit = 10; // Failsafe for (int) 'all' preventing limit=0 crashing pagination
        }
        if ($limit > 50) {
            $limit = 50; // Cap to prevent artificial crashes
        }
        $offset = (int) ($request['offset'] ?? 1);

        $products = CategoryManager::products($id, $request, $limit);
        $productFinal = ProductCategoryResource::collection($products->items());

        // Fetch sub-categories to display as sub-tabs in the mobile app alongside products
        $subCategories = \App\Models\Category::where('parent_id', $id)
            ->select(['id', 'name', 'slug', 'icon', 'parent_id', 'position'])
            ->orderByDesc('priority')
            ->get();

        $formattedSub = CategoryThinResource::collection($subCategories);

        return response()->json([
            'total_size' => $products->total(),
            'limit'      => $limit,
            'offset'     => $offset,
            'sub_categories' => $formattedSub,
            'products'   => $productFinal,
        ], 200);
    }

    public function find_what_you_need(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 1);

        $find_what_you_need_categories = Category::where('parent_id', 0)
            ->whereHas('childes')
            ->with(['childes' => function ($query) {
                $query->withCount(['subCategoryProduct' => function ($query) {
                    return $query->active();
                }]);
            }])
            ->withCount(['product' => function ($query) {
                return $query->active();
            }])
            ->paginate($limit, ['*'], 'page', $offset);

        $get_categories = [];
        foreach ($find_what_you_need_categories->items() as $category) {
            $categoryArray = $category->toArray();
            $categoryArray['childes'] = array_slice($categoryArray['childes'], 0, 4);
            $get_categories[] = $categoryArray;
        }

        return response()->json(['find_what_you_need' => $get_categories], 200);
    }

}
