@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('layouts.admin.app')

@section('title',$seller?->shop->name ?? translate("shop_name_not_found"))

@section('content')
    @php($direction = Session::get('direction'))
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png')}}" alt="">
                {{translate('vendor_Details')}}
            </h2>
        </div>

        <div class="flex-between d-sm-flex row align-items-center justify-content-between mb-2 mx-1">
            <div>
                @if ($seller->status=="pending")
                    <div class="mt-4 pe-2">
                        <div class="">
                            <div class="mx-1"><h4><i class="fi fi-rr-shop"></i></h4></div>
                            <div>{{translate('vendor_request_for_open_a_shop.')}}</div>
                        </div>
                        <div class="text-center">
                            <form class="d-inline-block" action="{{route('admin.vendors.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-primary btn-sm">{{translate('approve')}}</button>
                            </form>
                            <form class="d-inline-block" action="{{route('admin.vendors.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger btn-sm">{{translate('reject')}}</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="page-header mb-4">
            <h2 class="page-header-title mb-3">{{ $seller?->shop->name ?? translate("Shop_Name").' : '. translate("Update_Please") }}</h2>

            <div class="position-relative nav--tab-wrapper">
                <ul class="nav nav-pills nav--tab">
                    <li class="nav-item">
                        <a class="nav-link "
                           href="{{ route('admin.vendors.view',$seller->id) }}">{{translate('shop')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active"
                           href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'order']) }}">{{translate('order')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'product']) }}">{{translate('product')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.vendors.view',['id'=>$seller['id'], 'tab'=>'clearance_sale']) }}">{{translate('clearance_sale_products')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'setting']) }}">{{translate('setting')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'transaction']) }}">{{translate('transaction')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'review']) }}">{{translate('review')}}</a>
                    </li>
                </ul>
                <div class="nav--tab__prev">
                    <button class="btn btn-circle border-0 bg-white text-primary">
                        <i class="fi fi-sr-angle-left"></i>
                    </button>
                </div>
                <div class="nav--tab__next">
                    <button class="btn btn-circle border-0 bg-white text-primary">
                        <i class="fi fi-sr-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                    <h3 class="mb-0 text-capitalize">{{translate('order_info')}}</h3>

                    <a type="button" class="btn btn-outline-primary text-nowrap" href="{{route('admin.vendors.order-list-export',[$seller['id'],'searchValue' => request('searchValue')])}}">
                        <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}" class="excel" alt="">
                        <span class="ps-2">{{ translate('export') }}</span>
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="order-stats order-stats_pending">
                            <div class="order-stats__content">
                                <i class="fi fi-rr-humanitarian-mission"></i>
                                <h4 class="order-stats__subtitle">{{translate('pending')}}</h4>
                            </div>
                            <div class="order-stats__title">
                                {{ $pendingOrder }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="order-stats order-stats_delivered">
                            <div class="order-stats__content">
                                <i class="fi fi-rr-check-circle"></i>
                                <h4 class="order-stats__subtitle">{{translate('delivered')}}</h4>
                            </div>
                            <div class="order-stats__title">
                                {{ $deliveredOrder }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="order-stats order-stats_all">
                            <div class="order-stats__content">
                                <i class="fi fi-rr-table-layout"></i>
                                <h4 class="order-stats__subtitle">{{translate('all')}}</h4>
                            </div>
                            <div class="order-stats__title">
                                {{ $orders->total() }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive datatable-custom mt-4">
                    <table id="datatable"
                           style="text-align: {{$direction === "rtl" ? 'right' : 'left'}};"
                           class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('order')}}</th>
                            <th>{{translate('date')}}</th>
                            <th>{{translate('customer')}}</th>
                            <th>{{translate('payment_status')}}</th>
                            <th>{{translate('total')}}</th>
                            <th>{{translate('order_status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                        </thead>
                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status class-all">
                                <td>
                                    {{$orders->firstItem()+$key}}
                                </td>
                                <td>
                                    <a href="{{route('admin.vendors.order-details',['order_id'=>$order['id'],'vendor_id'=>$order['seller_id']])}}"
                                       class="text-dark text-hover-primary">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>{{date('d M Y',strtotime($order['created_at']))}},</div>
                                    <div>{{ date("h:i A",strtotime($order['created_at'])) }}</div>
                                </td>
                                <td>
                                    @if($order->is_guest)
                                        <strong class="title-name">{{translate('guest_customer')}}</strong>
                                    @elseif($order->customer_id == 0)
                                        <strong class="title-name">
                                            {{ translate('Walk-In-Customer') }}
                                        </strong>
                                    @else
                                        @if($order->customer)
                                            <a class="text-dark text-capitalize" href="{{route('admin.customer.view',['user_id'=>$order['customer_id']])}}">
                                                <strong class="title-name">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong>
                                            </a>
                                            <a class="d-block text-dark" href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                        @else
                                            <label class="badge badge-danger text-bg-danger">{{translate('invalid_customer_data')}}</label>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($order->payment_status=='paid')
                                        <span class="badge badge-info text-bg-info">{{translate('paid')}}</span>
                                    @else
                                        <span class="badge badge-danger text-bg-danger">{{translate('unpaid')}}
                                    </span>
                                    @endif
                                </td>
                                <td>{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['order_amount']))}}</td>
                                <td class="text-capitalize">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-info text-bg-info">
                                            {{translate('pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-info text-bg-info">
                                            {{translate('confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-warning text-bg-warning">
                                            {{translate('processing')}}
                                        </span>
                                    @elseif($order['order_status']=='out_for_delivery')
                                        <span class="badge badge-warning text-bg-warning">
                                            {{translate('out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-success text-bg-success">
                                            {{translate('delivered')}}
                                        </span>
                                    @else
                                        <span class="badge badge-danger text-bg-danger">
                                            {{translate(str_replace('_',' ',$order['order_status']))}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a title="{{translate('view')}}" class="btn btn-outline-primary icon-btn" href="{{route('admin.vendors.order-details',['order_id'=>$order['id'],'vendor_id'=>$order['seller_id']])}}">
                                           <i class="fi fi-rr-eye"></i>
                                        </a>

                                        <a class="btn btn-outline-success icon-btn" target="_blank" title="{{translate('invoice')}}" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                           <i class="fi fi-rr-down-to-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-end">
                        {!! $orders->links() !!}
                    </div>
                </div>

                @if(count($orders)==0)
                    @include('layouts.admin.partials._empty-state',['text'=>'no_order_found'],['image'=>'default'])
                @endif
            </div>
        </div>
    </div>
@endsection
