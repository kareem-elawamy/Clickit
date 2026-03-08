@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin.app')
@section('title',translate('refund_requests'))

@section('content')
    <div class="content container-fluid">

        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/refund-request.png') }}" alt="">
                {{ translate($status.'_'.'refund_Requests') }}
                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $refundList->total() }}</span>
            </h2>
        </div>
        <div class="card">
            <div class="p-3">
                <div class="row justify-content-between align-items-center">
                    <div class="col-12 col-md-4">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group flex-grow-1 max-w-280">
                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                       placeholder="{{ translate('search_by_order_id_or_refund_id') }}"
                                       aria-label="Search orders" value="{{ request('searchValue') }}">
                                <div class="input-group-append search-submit">
                                    <button type="submit">
                                        <i class="fi fi-rr-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 mt-3 col-md-8">
                        <div class="d-flex gap-3 justify-content-md-end">
                            <div class="dropdown">
                                <a type="button" class="btn btn-outline-primary text-nowrap"
                                   href="{{route('admin.refund-section.refund.export',['status'=>request('status'),'searchValue'=>request('searchValue'), 'type'=>request('type')]) }}">
                                    <img width="14"
                                         src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/excel.png') }}"
                                         class="excel" alt="">
                                    <span class="ps-2">{{ translate('export') }}</span>
                                </a>
                            </div>
                            <div class="select-wrapper">
                                <select name="" id="" class="form-select"
                                        onchange="location.href='{{ url()->current()  }}?type='+this.value">
                                    <option
                                        value="all" {{ request('type') == 'all' ?'selected':''}}>{{ translate('all') }}</option>
                                    <option
                                        value="admin" {{ request('type')== 'admin' ? 'selected':''}}>{{ translate('inhouse_Requests') }}</option>
                                    <option
                                        value="seller" {{ request('type') == 'seller' ? 'selected':''}}>{{ translate('vendor_Requests') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-borderless">
                    <thead class="text-capitalize">
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th class="text-center">{{ translate('refund_ID') }}</th>
                        <th>{{ translate('order_id') }} </th>
                        <th>{{ translate('product_info') }}</th>
                        <th>{{ translate('customer_info') }}</th>
                        <th class="text-end">{{ translate('total_amount') }}</th>
                        <th class="text-center">{{ translate('action') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($refundList as $key=>$refund)
                        <tr>
                            <td>{{ $refundList->firstItem()+$key}}</td>
                            <td class="text-center">
                                <a href="{{route('admin.refund-section.refund.details', ['id' => $refund['id']]) }}"
                                   class="text-dark hover-primary">
                                    {{ $refund->id}}
                                </a>
                            </td>
                            <td>
                                <a href="{{route('admin.orders.details',['id'=>$refund->order_id]) }}"
                                   class="text-dark hover-primary">
                                    {{ $refund->order_id }}
                                </a>
                            </td>
                            <td>
                                @if ($refund->product !=null)
                                    <div class="d-flex flex-nowrap gap-2">
                                        <div class="d-block w-max-content">
                                            <a href="{{route('admin.products.view',['addedBy' => ($refund->product->added_by =='seller'?'vendor' : 'in-house'),'id'=>$refund->product->id]) }}">
                                                <img
                                                    src="{{ getStorageImages(path: $refund?->product?->thumbnail_full_url, type: 'backend-product') }}"
                                                    class="avatar border" alt="">
                                            </a>
                                        </div>
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ route('admin.products.view',['addedBy'=>($refund->product->added_by =='seller'?'vendor' : 'in-house'),'id'=>$refund->product->id]) }}"
                                               class="text-dark fw-bold hover-primary">
                                                {{ Str::limit($refund->product->name, 35) }}
                                            </a>
                                            <span class="fs-12">
                                                {{ translate('QTY') }} : {{ $refund->orderDetails->qty }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    {{ translate('product_name_not_found') }}
                                @endif
                            </td>
                            <td>
                                @if ($refund->customer !=null)
                                    <div class="d-flex flex-column gap-1">
                                        <a href="{{route('admin.customer.view', [$refund->customer->id]) }}"
                                           class="text-dark fw-bold hover-primary">
                                            {{ $refund->customer->f_name. ' '. $refund->customer->l_name}}
                                        </a>
                                        @if($refund->customer->phone)
                                            <a href="tel:{{ $refund->customer->phone}}"
                                               class="text-dark hover-primary fs-12">
                                                {{ $refund->customer->phone}}
                                            </a>
                                        @else
                                            <a href="mailto:{{ $refund->customer['email'] }}"
                                               class="text-dark hover-primary fs-12">
                                                {{ $refund->customer['email'] }}
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <a href="javascript:" class="text-dark hover-primary">
                                        {{ translate('customer_not_found') }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1 text-end">
                                    <div>
                                        {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $refund->amount), currencyCode: getCurrencyCode()) }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a
                                        class="btn btn-outline-info btn-outline-info-dark icon-btn"
                                        title="{{ translate('view') }}"
                                        href="{{route('admin.refund-section.refund.details',['id'=>$refund['id']]) }}"
                                    >
                                        <i class="fi fi-sr-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mt-4">
                <div class="px-4 d-flex justify-content-lg-end">
                    {!! $refundList->links() !!}
                </div>
            </div>

            @if(count($refundList) == 0)
                @include('layouts.admin.partials._empty-state',['text'=>'no_refund_request_found'],['image'=>'default'])
            @endif
        </div>
    </div>
@endsection
