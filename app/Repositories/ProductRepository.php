<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Cart;
use App\Models\DealOfTheDay;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\Wishlist;
use App\Traits\CacheManagerTrait;
use App\Traits\ProductTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

class ProductRepository implements ProductRepositoryInterface
{
    use ProductTrait, CacheManagerTrait;

    public function __construct(
        private readonly Product          $product,
        private readonly Translation      $translation,
        private readonly Tag              $tag,
        private readonly Cart             $cart,
        private readonly Wishlist         $wishlist,
        private readonly FlashDealProduct $flashDealProduct,
        private readonly DealOfTheDay     $dealOfTheDay,
    ) {}

    public function addRelatedTags(object $request, object $product): void
    {
        $tagIds = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $value) {
                $tag = $this->tag->firstOrNew(
                    ['tag' => trim($value)]
                );
                $tag->save();
                $tagIds[] = $tag->id;
            }
        }
        $product->tags()->sync($tagIds);
    }

    public function add(array $data): string|object
    {
        cacheRemoveByType(type: 'products');
        return $this->product->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->product->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithCount(array $params, array $withCount = [], array $relations = []): ?Model
    {
        return $this->product->with($relations)->where($params)->withCount($withCount)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->product->withoutGlobalScopes()->where($params)->with($relations)->first();
    }

    public function getFirstWhereActive(array $params, array $relations = []): ?Model
    {
        return $this->product->active()->where($params)->with($relations)->first();
    }

    public function getWebFirstWhereActive(array $params, array $relations = [], array $withCount = []): ?Model
    {
        return $this->product->active()
            ->when(isset($relations['reviews']), function ($query) use ($relations) {
                return $query->with($relations['reviews']);
            })
            ->when(isset($relations['seller.shop']), function ($query) use ($relations) {
                return $query->with($relations['seller.shop']);
            })
            ->when(isset($relations['wishList']), function ($query) use ($relations, $params) {
                return $query->with([$relations['wishList'] => function ($query) use ($params) {
                    return $query->when(isset($params['customer_id']), function ($query) use ($params) {
                        return $query->where('customer_id', $params['customer_id']);
                    });
                }]);
            })
            ->when(isset($relations['compareList']), function ($query) use ($relations, $params) {
                return $query->with([$relations['compareList'] => function ($query) use ($params) {
                    return $query->when(isset($params['customer_id']), function ($query) use ($params) {
                        return $query->where('user_id', $params['customer_id']);
                    });
                }]);
            })
            ->when(isset($relations['digitalProductAuthors']), function ($query) use ($relations) {
                return $query->with($relations['digitalProductAuthors'], function ($query) {
                    return $query->with('author');
                });
            })
            ->when(isset($relations['digitalProductPublishingHouse']), function ($query) use ($relations) {
                return $query->with($relations['digitalProductPublishingHouse'], function ($query) {
                    return $query->with('publishingHouse');
                });
            })
            ->when(isset($relations['clearanceSale']), function ($query) use ($relations) {
                return $query->with(['clearanceSale' => function ($query) {
                    return $query->active();
                }]);
            })
            ->when(isset($params['id']), function ($query) use ($params) {
                return $query->where('id', $params['id']);
            })
            ->when(isset($params['slug']), function ($query) use ($params) {
                return $query->where('slug', $params['slug']);
            })
            ->when(isset($withCount['orderDetails']), function ($query) use ($withCount) {
                return $query->withCount($withCount['orderDetails']);
            })
            ->when(isset($withCount['wishList']), function ($query) use ($withCount) {
                return $query->withCount($withCount['wishList']);
            })
            ->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator|LazyCollection
    {
        $query = $this->product->with($relations);

        $request = [
            'query' => $query,
            'searchValue' => $searchValue,
            'filters' => $filters
        ];

        $query = Pipeline::send($request)
            ->through([
                \App\Filters\Product\FilterByAddedBy::class,
                \App\Filters\Product\FilterBySearch::class,
                \App\Filters\Product\FilterByCategory::class,
                \App\Filters\Product\FilterByStatusAndBrand::class,
            ])
            ->then(function ($request) {
                return $request['query'];
            });

        if (!empty($orderBy)) {
            $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
        }

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : ($dataLimit == 'cursor' ? $query->cursor() : $query->paginate($dataLimit)->appends($filters));
    }

    public function getListWithScope(array $orderBy = [], string $searchValue = null, string $scope = null, array $filters = [], array $whereIn = [], array $whereNotIn = [], array $relations = [], array $withCount = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator|LazyCollection
    {
        $query = $this->product->with($relations)
            ->when(isset($withCount['reviews']), function ($q) use ($withCount) {
                return $q->withCount($withCount['reviews']);
            });

        $request = [
            'query' => $query,
            'searchValue' => $searchValue,
            'scope' => $scope,
            'filters' => $filters,
            'whereIn' => $whereIn,
            'whereNotIn' => $whereNotIn
        ];

        $query = Pipeline::send($request)
            ->through([
                \App\Filters\Product\FilterByScope::class,
                \App\Filters\Product\FilterByAddedBy::class,
                \App\Filters\Product\FilterBySearch::class,
                \App\Filters\Product\FilterByPosSearch::class,
                \App\Filters\Product\FilterByCategory::class,
                \App\Filters\Product\FilterByStatusAndBrand::class,
                \App\Filters\Product\FilterByWhere::class,
            ])
            ->then(function ($request) {
                return $request['query'];
            });

        if (!empty($orderBy)) {
            $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
        }

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : ($dataLimit == 'cursor' ? $query->cursor() : $query->paginate($dataLimit)->appends($filters));
    }

    public function getWebListWithScope(array $orderBy = [], string $searchValue = null, string $scope = null, array $filters = [], array $whereHas = [], array $whereIn = [], array $whereNotIn = [], array $relations = [], array $withCount = [], array $withSum = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator|LazyCollection
    {
        $query = $this->product
            ->when(isset($relations['reviews']), function ($query) use ($relations) {
                return $query->with($relations['reviews'], function ($query) use ($relations) {
                    return $query->active();
                });
            })
            ->when(isset($relations['seller.shop']), function ($query) use ($relations) {
                return $query->with($relations['seller.shop']);
            })
            ->when(isset($relations['flashDealProducts.flashDeal']), function ($query) use ($relations) {
                return $query->with($relations['flashDealProducts.flashDeal']);
            })
            ->when(isset($relations['wishList']), function ($query) use ($relations, $filters) {
                return $query->with([$relations['wishList'] => function ($query) use ($filters) {
                    return $query->when(isset($filters['customer_id']), function ($query) use ($filters) {
                        return $query->where('customer_id', $filters['customer_id']);
                    });
                }]);
            })
            ->when(isset($relations['compareList']), function ($query) use ($relations, $filters) {
                return $query->with([$relations['compareList'] => function ($query) use ($filters) {
                    return $query->when(isset($filters['customer_id']), function ($query) use ($filters) {
                        return $query->where('user_id', $filters['customer_id']);
                    });
                }]);
            })
            ->when(isset($whereHas['reviews']), function ($query) use ($whereHas) {
                return $query;
            })
            ->when(isset($withCount['reviews']), function ($query) use ($withCount) {
                return $query->withCount([$withCount['reviews'] => function ($query) {
                    return $query->active();
                }]);
            })
            ->when($withSum, function ($query) use ($withSum) {
                foreach ($withSum as $sum) {
                    return $query->withSum($sum['relation'], $sum['column'], function ($query) use ($sum) {
                        $query->where($sum['whereColumn'], $sum['whereValue']);
                    });
                }
                return $query->withSum($withSum['orderDetails']);
            })
            ->when(isset($withSum['qty']), function ($query) use ($withSum) {
                return $query->withSum($withSum['qty']);
            });

        $request = [
            'query' => $query,
            'searchValue' => $searchValue,
            'scope' => $scope,
            'filters' => $filters,
            'whereIn' => $whereIn,
            'whereNotIn' => $whereNotIn
        ];

        $query = Pipeline::send($request)
            ->through([
                \App\Filters\Product\FilterByScope::class,
                \App\Filters\Product\FilterByAddedBy::class,
                \App\Filters\Product\FilterBySearch::class,
                \App\Filters\Product\FilterByCategory::class,
                \App\Filters\Product\FilterByStatusAndBrand::class,
                \App\Filters\Product\FilterByWhere::class,
            ])
            ->then(function ($request) {
                return $request['query'];
            });

        if (!empty($orderBy)) {
            $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
        }

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : ($dataLimit == 'cursor' ? $query->cursor() : $query->paginate($dataLimit)->appends($filters));
    }

    public function update(string $id, array $data): bool
    {
        cacheRemoveByType(type: 'products');
        return $this->product->find($id)->update($data);
    }

    public function updateByParams(array $params, array $data): bool
    {
        cacheRemoveByType(type: 'products');
        return $this->product->where($params)->update($data);
    }

    public function getListWhereNotIn(array $filters = [], array $whereNotIn = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator|LazyCollection
    {
        $query = $this->product->when($whereNotIn, function ($query) use ($whereNotIn) {
            foreach ($whereNotIn as $key => $whereNotInIndex) {
                $query->whereNotIn($key, $whereNotInIndex);
            }
        })->when(isset($filters['user_id']), function ($query) use ($filters) {
            return $query->where(['user_id' => $filters['user_id']]);
        })->when(isset($filters['added_by']), function ($query) use ($filters) {
            return $query->where(['added_by' => $filters['added_by']]);
        });

        // PERF-10: Respect dataLimit parameter instead of hardcoding ->get()
        return $dataLimit == 'all' ? $query->get() : ($dataLimit == 'cursor' ? $query->cursor() : $query->paginate($dataLimit));
    }

    public function getTopRatedList(array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator|LazyCollection
    {
        $query = $this->product->with($relations)->where($filters)
            ->with('reviews', function ($query) {
                return $query->whereHas('product', function ($query) {
                    $query->active();
                });
            })
            ->withCount(['reviews' => function ($query) {
                return $query->whereNull('delivery_man_id');
            }])
            ->withAvg('rating as ratings_average', 'rating')
            ->orderByDesc('reviews_count');

        if ($dataLimit === 'all') {
            return $query->get();
        } else if ($dataLimit === 'cursor') {
            return $query->cursor();
        }

        return $query->paginate($dataLimit, ['*'], 'page', $offset)->appends(request()->query());
    }

    public function getTopSellList(array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator|LazyCollection
    {
        $query = $this->product->with($relations)
            ->when(isset($filters['added_by']) && $this->isAddedByInHouse(addedBy: $filters['added_by']), function ($query) {
                return $query->where(['added_by' => 'admin']);
            })->when(isset($filters['added_by']) && !$this->isAddedByInHouse($filters['added_by']), function ($query) use ($filters) {
                return $query->where(['added_by' => 'seller', 'request_status' => $filters['request_status']]);
            })->when(isset($filters['seller_id']), function ($query) use ($filters) {
                return $query->where('user_id', $filters['seller_id']);
            })
            ->when(isset($filters['request_status']), function ($query) use ($filters) {
                return $query->where('request_status', $filters['request_status']);
            })
            ->whereHas('orderDetails', function ($query) {
                $query->where(['delivery_status' => 'delivered']);
            })
            ->withCount('orderDetails')
            ->orderByDesc('order_details_count');

        if ($dataLimit === 'all') {
            return $query->get();
        } else if ($dataLimit === 'cursor') {
            return $query->cursor();
        }

        return $query->paginate($dataLimit, ['*'], 'page', $offset)->appends(request()->query());
    }

    public function delete(array $params): bool
    {
        cacheRemoveByType(type: 'products');
        return $this->product->where($params)->delete();
    }

    public function getStockLimitListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $withCount = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator|LazyCollection
    {
        $stockLimit = $filters['current_stock'];
        $query = $this->product->with($relations)
            ->withCount($withCount)
            ->when($this->isAddedByInHouse(addedBy: $filters['added_by']), function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when(!$this->isAddedByInHouse($filters['added_by']), function ($query) use ($filters) {
                return $query->where(['added_by' => 'seller', 'product_type' => 'physical'])
                    ->when(isset($filters['request_status']), function ($query) use ($filters) {
                        return $query->where(['request_status' => $filters['request_status']]);
                    })
                    ->when(isset($filters['seller_id']), function ($query) use ($filters) {
                        return $query->where(['user_id' => $filters['seller_id']]);
                    });
            })
            ->when(isset($filters['product_type']), function ($query) use ($filters) {
                return $query->where(['product_type' => $filters['product_type']]);
            })
            ->when($searchValue, function ($query) use ($searchValue) {
                $product_ids = $this->translation->where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where('value', 'like', "%{$searchValue}%")
                    ->pluck('translationable_id');

                return $query->where('name', 'like', "%{$searchValue}%")
                    ->where(function ($query) use ($product_ids) {
                        return $query->orWhereIn('id', $product_ids);
                    });
            })
            ->when($stockLimit <= 0, function ($query) {
                return $query->where('current_stock', 0);
            })
            ->when($stockLimit > 0, function ($query) use ($stockLimit) {
                return $query->where('current_stock', '<', $stockLimit);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : ($dataLimit == 'cursor' ? $query->cursor() : $query->paginate($dataLimit)->appends($filters));
    }

    public function getProductIds(array $filters = []): \Illuminate\Support\Collection|array
    {
        return $this->product->when(isset($filters['added_by']), function ($query) use ($filters) {
            return $query->where('added_by', $filters['added_by']);
        })->when(isset($filters['user_id']), function ($query) use ($filters) {
            return $query->where('user_id', $filters['user_id']);
        })->pluck('id');
    }

    public function addArray(array $data): bool
    {
        cacheRemoveByType(type: 'products');
        return DB::table('products')->insert($data);
    }
}
