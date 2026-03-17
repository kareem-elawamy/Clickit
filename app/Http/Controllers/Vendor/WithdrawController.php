<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Repositories\VendorWithdrawMethodInfoRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\VendorWalletRepositoryInterface;
use App\Contracts\Repositories\WithdrawalMethodRepositoryInterface;
use App\Contracts\Repositories\WithdrawRequestRepositoryInterface;
use App\Exports\VendorWithdrawRequest;
use App\Http\Controllers\BaseController;
use App\Services\VendorWalletService;
use Illuminate\Database\Eloquent\Collection;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WithdrawController extends BaseController
{
    /**
     * @param WithdrawRequestRepositoryInterface $withdrawRequestRepo
     * @param VendorWalletRepositoryInterface $vendorWalletRepo
     * @param VendorWalletService $vendorWalletService
     * @param VendorRepositoryInterface $vendorRepo
     * @param VendorWithdrawMethodInfoRepositoryInterface $vendorWithdrawMethodInfoRepo
     * @param WithdrawalMethodRepositoryInterface $withdrawalMethodRepo
     */
    public function __construct(
        private readonly WithdrawRequestRepositoryInterface          $withdrawRequestRepo,
        private readonly VendorWalletRepositoryInterface             $vendorWalletRepo,
        private readonly VendorWalletService                         $vendorWalletService,
        private readonly VendorRepositoryInterface                   $vendorRepo,
        private readonly VendorWithdrawMethodInfoRepositoryInterface $vendorWithdrawMethodInfoRepo,
        private readonly WithdrawalMethodRepositoryInterface         $withdrawalMethodRepo,
    )
    {

    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|null
     */
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable
    {
        if ($request->has('status')) {
            $filters['status'] = $request['status'];
        }
        $vendorId = auth('seller')->id();
        $vendorWallet = $this->vendorWalletRepo->getFirstWhere(params: ['seller_id' => $vendorId]);

        $withdrawRequests = $this->withdrawRequestRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['search'] ?? null,
            filters: array_merge(['vendorId' => $vendorId], $filters ?? []),
            relations: ['seller'],
            dataLimit: getWebConfig('pagination_limit')
        );


        $withdrawalMethods = $this->withdrawalMethodRepo->getListWhere(filters: ['is_active' => 1], dataLimit: 'all');
        $vendorWithdrawMethods = $this->vendorWithdrawMethodInfoRepo->getListWhere(
            orderBy: ['method_name' => 'asc'],
            filters: ['user_id' => $vendorId, 'is_active' => 1],
            relations: ['withdraw_method'],
            dataLimit: 'all'
        );

        return view('vendor-views.withdraw.index', [
            'vendorWallet' => $vendorWallet,
            'withdrawRequests' => $withdrawRequests,
            'withdrawalMethods' => $withdrawalMethods,
            'vendorWithdrawMethods' => $vendorWithdrawMethods,
        ]);
    }

    public function renderInfosView(Request $request): JsonResponse
    {
        $vendorId = auth('seller')->id();
        $vendorWallet = $this->vendorWalletRepo->getFirstWhere(params: ['seller_id' => $vendorId]);

        if ($request['method_type'] == 'custom') {
            $vendorWithdrawMethod = $this->vendorWithdrawMethodInfoRepo->getFirstWhere(
                params: ['user_id' => $vendorId, 'id' => $request['method_id']],
                relations: ['withdraw_method'],
            );
            $withdrawalMethod = $this->withdrawalMethodRepo->getFirstWhere(params: ['id' => $vendorWithdrawMethod['withdraw_method_id']]);
            return response()->json([
                'htmlView' => view('vendor-views.withdraw._withdraw-request-method-filed', [
                    'method_type' => $request['method_type'],
                    'vendorWallet' => $vendorWallet,
                    'withdrawalMethod' => $withdrawalMethod,
                    'vendorWithdrawMethod' => $vendorWithdrawMethod,
                ])->render(),
            ]);
        } else {
            $withdrawalMethod = $this->withdrawalMethodRepo->getFirstWhere(params: ['id' => $request['method_id']]);
            return response()->json([
                'htmlView' => view('vendor-views.withdraw._withdraw-request-method-filed', [
                    'method_type' => $request['method_type'],
                    'vendorWallet' => $vendorWallet,
                    'withdrawalMethod' => $withdrawalMethod,
                ])->render(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListByStatus(Request $request): JsonResponse
    {
        $vendorId = auth('seller')->id();
        $withdrawRequests = $this->withdrawRequestRepo->getListWhere(
            filters: [
                'vendorId' => $vendorId,
                'status' => $request['status']
            ],
            relations: ['seller'],
            dataLimit: getWebConfig('pagination_limit')
        );
        return response()->json([
            'view' => view('vendor-views.withdraw._table', compact('withdrawRequests'))->render(),
            'count' => $withdrawRequests->count(),
        ], 200);
    }

    /**
     * @param string|int $id
     * @return RedirectResponse
     */
    public function closeWithdrawRequest(string|int $id): RedirectResponse
    {
        $withdrawRequest = $this->withdrawRequestRepo->getFirstWhere(params: ['id' => $id]);
        $wallet = $this->vendorWalletRepo->getFirstWhere(params: ['seller_id' => auth('seller')->id()]);
        if ($withdrawRequest['approved'] == 0) {
            $totalEarning = $wallet['total_earning'] + currencyConverter(amount: $withdrawRequest['amount']);
            $pendingWithdraw = $wallet['pending_withdraw'] - currencyConverter(amount: $withdrawRequest['amount']);
            $this->vendorWalletRepo->update(
                id: $wallet['id'],
                data: $this->vendorWalletService->getVendorWalletData(
                    totalEarning: $totalEarning,
                    pendingWithdraw: $pendingWithdraw
                )
            );
            $this->withdrawRequestRepo->delete(['id' => $withdrawRequest['id']]);
            ToastMagic::success(message: translate('request_closed') . '!');
        } else {
            ToastMagic::error(message: translate('invalid_request'));
        }
        return redirect()->back();
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $vendorId = auth('seller')->id();
        $vendor = $this->vendorRepo->getFirstWhere(params: ['id' => $vendorId]);
        
        $withdrawRequestsQuery = \App\Models\WithdrawRequest::with(['seller'])
            ->where('vendor_id', $vendorId)
            ->when($request['status'] && $request['status'] != 'all', function($q) use ($request) {
                $statusMap = ['pending' => 0, 'approved' => 1, 'denied' => 2];
                if (isset($statusMap[$request['status']])) {
                    $q->where('approved', $statusMap[$request['status']]);
                }
            })
            ->when($request['searchValue'], function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('id', 'like', "%{$request['searchValue']}%")
                          ->orWhere('amount', 'like', "%{$request['searchValue']}%");
                });
            })
            ->orderBy('id', 'desc');

        $pendingRequest = (clone $withdrawRequestsQuery)->where('approved', 0)->count();
        $approvedRequest = (clone $withdrawRequestsQuery)->where('approved', 1)->count();
        $deniedRequest = (clone $withdrawRequestsQuery)->where('approved', 2)->count();
        $totalCount = $withdrawRequestsQuery->count();

        $withdrawRequests = $withdrawRequestsQuery->cursor();

        $data = [
            'data-from' => 'vendor',
            'vendor' => $vendor,
            'withdraw_request' => $withdrawRequests,
            'filter' => $request['status'],
            'searchValue' => $request['searchValue'],
            'pending' => $pendingRequest,
            'approved' => $approvedRequest,
            'denied' => $deniedRequest,
            'total_count' => $totalCount,
        ];
        return Excel::download(export: new VendorWithdrawRequest($data), fileName: 'Vendor-Withdraw-Request.xlsx');
    }


}
