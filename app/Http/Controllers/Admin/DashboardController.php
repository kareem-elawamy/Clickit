<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\AdminWalletRepositoryInterface;
use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\DeliveryManRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\OrderTransactionRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\RestockProductRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\VendorWalletRepositoryInterface;
use App\Http\Controllers\BaseController;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends BaseController
{
    public function __construct(
        private readonly AdminWalletRepositoryInterface      $adminWalletRepo,
        private readonly CustomerRepositoryInterface         $customerRepo,
        private readonly OrderTransactionRepositoryInterface $orderTransactionRepo,
        private readonly ProductRepositoryInterface          $productRepo,
        private readonly DeliveryManRepositoryInterface      $deliveryManRepo,
        private readonly OrderRepositoryInterface            $orderRepo,
        private readonly BrandRepositoryInterface            $brandRepo,
        private readonly VendorRepositoryInterface           $vendorRepo,
        private readonly VendorWalletRepositoryInterface     $vendorWalletRepo,
        private readonly RestockProductRepositoryInterface   $restockProductRepo,
        private readonly DashboardService                    $dashboardService,
    ) {}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        // PERF-7: Removed ->take() and passed dataLimit directly to the repositories
        $mostRatedProducts = $this->productRepo->getTopRatedList(dataLimit: DASHBOARD_DATA_LIMIT);
        $topSellProduct = $this->productRepo->getTopSellList(relations: ['orderDetails'], dataLimit: DASHBOARD_TOP_SELL_DATA_LIMIT);
        $topCustomer = $this->orderRepo->getTopCustomerList(relations: ['customer'], dataLimit: DASHBOARD_DATA_LIMIT);
        $topRatedDeliveryMan = $this->deliveryManRepo->getTopRatedList(filters: ['seller_id' => 0], relations: ['deliveredOrders'], dataLimit: DASHBOARD_DATA_LIMIT);
        $topVendorByEarning = $this->vendorWalletRepo->getListWhere(orderBy: ['total_earning' => 'desc'], filters: [['column' => 'total_earning', 'operator' => '>', 'value' => 0]], relations: ['seller.shop'], dataLimit: DASHBOARD_DATA_LIMIT);
        $topVendorByOrderReceived = $this->vendorRepo->getTopVendorListByWishlist(relations: ['shop'], dataLimit: DASHBOARD_DATA_LIMIT);
        $data = self::getOrderStatusData();
        $admin_wallet = $this->adminWalletRepo->getFirstWhere(params: ['admin_id' => 1]);

        $from = now()->startOfYear()->format('Y-m-d');
        $to = now()->endOfYear()->format('Y-m-d');
        $range = range(1, 12);
        $label = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $inHouseOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: 'month', userType: 'admin');
        $vendorOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: 'month', userType: 'seller');
        $inHouseEarning = $this->getEarning(from: $from, to: $to, range: $range, type: 'month', userType: 'admin');
        $vendorEarning = $this->getEarning(from: $from, to: $to, range: $range, type: 'month', userType: 'seller');
        $commissionEarn = $this->getAdminCommission(from: $from, to: $to, range: $range, type: 'month');
        // PERF-6: Replaced repo->getListWhere(dataLimit: 'all')->count() memory leaks with DB level counts
        $dateType = 'yearEarn';
        $getTotalCustomerCount = \App\Models\User::where('is_active', 1)->count();
        $data += [
            'order' => \App\Models\Order::count(),
            'brand' => \App\Models\Brand::count(),
            'topSellProduct' => $topSellProduct,
            'mostRatedProducts' => $mostRatedProducts,
            'topVendorByEarning' => $topVendorByEarning,
            'top_customer' => $topCustomer,
            'top_store_by_order_received' => $topVendorByOrderReceived,
            'topRatedDeliveryMan' => $topRatedDeliveryMan,
            'inhouse_earning' => $admin_wallet['inhouse_earning'] ?? 0,
            'commission_earned' => $admin_wallet['commission_earned'] ?? 0,
            'delivery_charge_earned' => $admin_wallet['delivery_charge_earned'] ?? 0,
            'pending_amount' => $admin_wallet['pending_amount'] ?? 0,
            'total_tax_collected' => $admin_wallet['total_tax_collected'] ?? 0,
            'getTotalCustomerCount' => $getTotalCustomerCount,
            'getTotalVendorCount' => \App\Models\Seller::count(),
            'getTotalDeliveryManCount' => \App\Models\DeliveryMan::where('seller_id', 0)->count(),
        ];
        return view('admin-views.system.dashboard', compact('data', 'inHouseEarning', 'vendorEarning', 'commissionEarn', 'inHouseOrderEarningArray', 'vendorOrderEarningArray', 'label', 'dateType'));
    }

    public function getOrderStatus(Request $request)
    {
        session()->put('statistics_type', $request['statistics_type']);
        $data = self::getOrderStatusData();
        return response()->json(['view' => view('admin-views.partials._dashboard-order-status', compact('data'))->render()], 200);
    }


    public function getOrderStatusData(): array
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today';
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month';

        // 1. الضربة القاضية: استعلام واحد مجمع لكل حالات الطلبات
        $orderStatuses = \App\Models\Order::query()
            ->when($today, function ($q) {
                return $q->whereDate('created_at', now());
            })
            ->when($this_month, function ($q) {
                return $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            })
            ->select('order_status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        // 2. الاستعلامات الأساسية لباقي الكيانات
        $orderQuery = \App\Models\Order::query();
        $storeQuery = \App\Models\Seller::query();
        $productQuery = \App\Models\Product::query();
        $customerQuery = \App\Models\User::where('is_active', 1);

        return [
            'order' => self::getCommonQueryOrderStatus(clone $orderQuery),
            'store' => self::getCommonQueryOrderStatus(clone $storeQuery),
            'product' => self::getCommonQueryOrderStatus(clone $productQuery),
            'customer' => self::getCommonQueryOrderStatus(clone $customerQuery),

            // 3. قراءة الأرقام من المصفوفة المجمعة (صفر استعلامات إضافية هنا!)
            'failed' => $orderStatuses['failed'] ?? 0,
            'pending' => $orderStatuses['pending'] ?? 0,
            'returned' => $orderStatuses['returned'] ?? 0,
            'canceled' => $orderStatuses['canceled'] ?? 0,
            'confirmed' => $orderStatuses['confirmed'] ?? 0,
            'delivered' => $orderStatuses['delivered'] ?? 0,
            'processing' => $orderStatuses['processing'] ?? 0,
            'out_for_delivery' => $orderStatuses['out_for_delivery'] ?? 0,
        ];
    }

    public function getCommonQueryOrderStatus($query)
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        // Uses DB-level Eloquent condition and DB-level count
        return $query->when($today, function ($q) {
            return $q->where('created_at', '>=', now()->startOfDay())
                ->where('created_at', '<', now()->endOfDay());
        })->when($this_month, function ($q) {
            return $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        })->count();
    }

    public function getOrderStatistics(Request $request): JsonResponse
    {
        $dateType = $request['type'];
        $dateTypeArray = $this->dashboardService->getDateTypeData(dateType: $dateType);
        $from = $dateTypeArray['from'];
        $to = $dateTypeArray['to'];
        $type = $dateTypeArray['type'];
        $range = $dateTypeArray['range'];
        $inHouseOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: $type, userType: 'admin');
        $vendorOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: $type, userType: 'seller');
        $label = $dateTypeArray['keyRange'] ?? [];
        $inHouseOrderEarningArray = array_values($inHouseOrderEarningArray);
        $vendorOrderEarningArray = array_values($vendorOrderEarningArray);
        return response()->json([
            'view' => view('admin-views.system.partials.order-statistics', compact('inHouseOrderEarningArray', 'vendorOrderEarningArray', 'label', 'dateType'))->render(),
        ]);
    }

    public function getEarningStatistics(Request $request): JsonResponse
    {
        $dateType = $request['type'];
        $dateTypeArray = $this->dashboardService->getDateTypeData(dateType: $dateType);
        $from = $dateTypeArray['from'];
        $to = $dateTypeArray['to'];
        $type = $dateTypeArray['type'];
        $range = $dateTypeArray['range'];
        $inHouseEarning = $this->getEarning(from: $from, to: $to, range: $range, type: $type, userType: 'admin');
        $vendorEarning = $this->getEarning(from: $from, to: $to, range: $range, type: $type, userType: 'seller');
        $commissionEarn = $this->getAdminCommission(from: $from, to: $to, range: $range, type: $type);
        $label = $dateTypeArray['keyRange'] ?? [];
        $inHouseEarning = array_values($inHouseEarning);
        $vendorEarning = array_values($vendorEarning);
        $commissionEarn = array_values($commissionEarn);
        return response()->json([
            'view' => view('admin-views.system.partials.earning-statistics', compact('inHouseEarning', 'vendorEarning', 'commissionEarn', 'label', 'dateType'))->render(),
        ]);
    }

    protected function getOrderStatisticsData($from, $to, $range, $type, $userType): array
    {
        $orderEarnings = $this->orderRepo->getListWhereBetween(
            filters: [
                'seller_is' => $userType,
                'payment_status' => 'paid'
            ],
            selectColumn: 'order_amount',
            whereBetween: 'created_at',
            whereBetweenFilters: [$from, $to],
        );
        $orderEarningArray = [];
        foreach ($range as $value) {
            $matchingEarnings = $orderEarnings->where($type, $value);
            if ($matchingEarnings->count() > 0) {
                $orderEarningArray[$value] = usdToDefaultCurrency($matchingEarnings->sum('sums'));
            } else {
                $orderEarningArray[$value] = 0;
            }
        }
        return $orderEarningArray;
    }

    protected function getEarning(string|Carbon $from, string|Carbon $to, array $range, string $type, $userType): array
    {
        $earning = $this->orderTransactionRepo->getListWhereBetween(
            filters: [
                'seller_is' => $userType,
                'status' => 'disburse',
            ],
            selectColumn: 'seller_amount',
            whereBetween: 'created_at',
            groupBy: $type,
            whereBetweenFilters: [$from, $to],
        );
        return $this->dashboardService->getDateWiseAmount(range: $range, type: $type, amountArray: $earning);
    }

    /**
     * @param string|Carbon $from
     * @param string|Carbon $to
     * @param array $range
     * @param string $type
     * @return array
     */
    protected function getAdminCommission(string|Carbon $from, string|Carbon $to, array $range, string $type): array
    {
        $commissionGiven = $this->orderTransactionRepo->getListWhereBetween(
            filters: [
                'seller_is' => 'seller',
                'status' => 'disburse',
            ],
            selectColumn: 'admin_commission',
            whereBetween: 'created_at',
            groupBy: $type,
            whereBetweenFilters: [$from, $to],
        );
        return $this->dashboardService->getDateWiseAmount(range: $range, type: $type, amountArray: $commissionGiven);
    }

    public function getRealTimeActivities(): JsonResponse
    {
        $newOrder = \App\Models\Order::where('checked', 0)->count();
        $restockProductList = \App\Models\RestockProduct::where('added_by', 'in_house')->select('product_id')->groupBy('product_id')->get();
        $restockProduct = [];
        if (count($restockProductList) == 1) {
            $products = $this->restockProductRepo->getListWhere(orderBy: ['updated_at' => 'desc'], filters: ['added_by' => 'in_house'], relations: ['product'], dataLimit: 'all');
            $firstProduct = $products->first();
            $count = $products?->sum('restock_product_customers_count') ?? 0;
            $restockProduct = [
                'title' => $firstProduct?->product?->name ?? '',
                'body' => $count < 100 ? translate('This_product_has') . ' ' . $count . ' ' . translate('restock_request') : translate('This_product_has') . ' 99+ ' . translate('restock_request'),
                'image' => getStorageImages(path: $firstProduct?->product?->thumbnail_full_url ?? '', type: 'product'),
                'route' => route('admin.products.request-restock-list')
            ];
        } elseif (count($restockProductList) > 1) {
            $restockProduct = [
                'title' => translate('Restock_Request'),
                'body' => count($restockProductList) < 100 ? (count($restockProductList) . ' ' . translate('products_have_restock_request')) : ('99 +' . ' ' . translate('more_products_have_restock_request')),
                'image' => dynamicAsset(path: 'public/assets/back-end/img/icons/restock-request-icon.svg'),
                'route' => route('admin.products.request-restock-list')
            ];
        }

        return response()->json([
            'success' => 1,
            'new_order_count' => $newOrder,
            'restockProductCount' => $restockProductList->count(),
            'restockProduct' => $restockProduct
        ]);
    }
}
