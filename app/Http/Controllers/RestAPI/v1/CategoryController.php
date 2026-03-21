<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Utils\CategoryManager;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get_categories(Request $request): JsonResponse
    {
        $categoriesID = [];
        if ($request->has('seller_id') && $request['seller_id'] != null) {
            $categoriesID = Product::active()
                ->when($request->has('seller_id') && $request['seller_id'] != null && $request['seller_id'] != 0, function ($query) use ($request) {
                    return $query->where(['added_by' => 'seller'])
                        ->where('user_id', $request['seller_id']);
                })->when($request->has('seller_id') && $request['seller_id'] != null && $request['seller_id'] == 0, function ($query) use ($request) {
                    return $query->where(['added_by' => 'admin',
                    ]);
                })->pluck('category_id');
        }

        $categories = Category::when($request->has('seller_id') && $request['seller_id'] != null, function ($query) use ($categoriesID) {
            return $query->whereIn('id', $categoriesID);
        })
            ->with(['product' => function ($query) {
                // Select only what's needed for the order matching count, stop pulling full product objects to memory
                return $query->select('id', 'category_id', 'added_by', 'user_id')->active()->withCount(['orderDetails']);
            }])
            ->withCount(['product' => function ($query) use ($request) {
                return $query->active()->when($request->has('seller_id') && !empty($request['seller_id']), function ($query) use ($request) {
                    return $query->where(['added_by' => 'seller', 'user_id' => $request['seller_id'], 'status' => '1']);
                });
            }])->with(['childes' => function ($query) {
                return $query->with(['childes' => function ($query) {
                    return $query->withCount(['subSubCategoryProduct' => function ($query) {
                        return $query->active();
                    }])->where('position', 2);
                }])->withCount(['subCategoryProduct' => function ($query) {
                    return $query->active();
                }])->where('position', 1);
            }, 'childes.childes'])
            ->where(['position' => 0])->get();

        $categories = CategoryManager::getPriorityWiseCategorySortQuery(query: $categories);

        // Remove the massive product relation from the JSON response, we only needed it for sorting
        $categories->map(function ($category) {
            // ISSUE 2: Preserve JSON structure for mobile clients without the bloat
            $category->setRelation('product', collect());
            return $category;
        });

        return response()->json($categories->values());
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
        $productFinal = Helpers::product_data_formatting($products->items(), true);
        
        // STRICT PAYLOAD SCRUBBING: Preserve structural JSON keys to prevent Mobile App crashes
        $productFinal = Helpers::product_payload_scrub($productFinal);

        // ISSUE 2 FIX: Mock the legacy 'all' structural response if no limit was explicitly requested, 
        // to prevent the mobile app from crashing by feeding it a raw Array instead of a nested Object.
        $requestedLimit = $request['limit'] ?? 'all';
        if ($requestedLimit === 'all') {
            return response()->json(array_values($productFinal), 200);
        }

        return response()->json([
            'total_size' => $products->total(),
            'limit'      => $limit,
            'offset'     => $offset,
            'products'   => array_values($productFinal),
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
