@extends('layouts.admin.app')

@section('title', translate('customer_Details'))

@push('css_or_js')
    <link rel="stylesheet" href="{{dynamicAsset(path:'public/assets/back-end/css/owl.min.css')}}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <div class="mb-3">
                        <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png')}}" alt="">
                            {{translate('customer_details')}}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6 col-xxl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="mb-4 d-flex align-items-center gap-2">
                            <img class="rounded" src="{{dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png')}}"
                                 alt="">
                            {{translate('customer').' # '.$customer['id']}}
                        </h3>

                        <div class="d-flex gap-3 flex-wrap">
                            <img src="{{ getStorageImages(path: $customer->image_full_url , type: 'backend-profile') }}" alt="{{translate('image')}}" class="rounded aspect-1 w-100px">
                            <div class="customer-details-new-card-content">
                                <h4 class="name line-2" data-bs-toggle="tooltip" data-bs-title="{{$customer['f_name'].' '.$customer['l_name']}}">{{$customer['f_name'].' '.$customer['l_name']}}</h4>
                                <ul class="customer-details-new-card-content-list">
                                    <li>
                                        <span class="key">{{translate('contact')}}</span>
                                        <span class="me-3">:</span>
                                        <strong class="value">{{!empty($customer['phone']) ? $customer['phone'] : translate('no_data_found')}}</strong>
                                    </li>
                                    <li>
                                        <span class="key">{{translate('email')}}</span>
                                        <span class="me-3">:</span>
                                        <strong class="value">{{$customer['email'] ?? translate('no_data_found')}}</strong>
                                    </li>
                                    <li>
                                        <span class="key text-capitalize">{{translate('joined_date')}}</span>
                                        <span class="me-3">:</span>
                                        <strong class="value">{{date('d M Y',strtotime($customer['created_at']))}}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($customer->addresses)>0)
                <div class="col-xl-6 col-xxl-8">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-4 d-flex align-items-center gap-2 text-capitalize">{{translate('saved_address')}}</h3>
                            <div class="swiper addressSwiper">
                                <div class="swiper-wrapper">
                                    @foreach($customer->addresses as $address)
                                        <div class="swiper-slide p-3 rounded bg-section">
                                            <div class="customer-details-new-card-content">
                                                <h4 class="name text-capitalize mb-2">{{$address['address_type'].' ( '.translate($address['is_billing'] == 0 ? 'shipping_address': 'billing_address').' )'}} </h4>
                                                <ul class="customer-details-new-card-content-list">
                                                    <li>
                                                        <strong class="key">{{translate('name')}}</strong>
                                                        <span class="me-3">:</span>
                                                        <span class="value">{{$address['contact_person_name']}}</span>
                                                    </li>
                                                    <li>
                                                        <strong class="key">{{translate('phone')}}</strong>
                                                        <span class="me-3">:</span>
                                                        <span class="value">{{$address['phone']}}</span>
                                                    </li>
                                                    <li>
                                                        <strong class="key">{{translate('address')}}</strong>
                                                        <span class="me-3">:</span>
                                                        <span class="value">{{$address['address']}}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-xl-6 col-xxl-8 col--xxl-8 d-none d-lg-block">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4">
                                <a href="" class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/total-order.png')}}" alt="">
                                        <h6 class="order-stats__subtitle text-capitalize">{{translate('total_orders')}}</h6>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['total_order']}}</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/ongoing.png')}}" alt="">
                                        <h6 class="order-stats__subtitle">{{translate('ongoing')}}</h6>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['ongoing']}}</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/completed.png')}}" alt="">
                                        <h6 class="order-stats__subtitle">{{translate('completed')}}</h6>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['completed']}}</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/canceled.png')}}" alt="">
                                        <h6 class="order-stats__subtitle">{{translate('canceled')}}</h6>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['canceled']}}</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/returned.png')}}" alt="">
                                        <h6 class="order-stats__subtitle">{{translate('returned')}}</h6>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['returned']}}</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/failed.png')}}" alt="">
                                        <h6 class="order-stats__subtitle">{{translate('failed')}}</h6>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['failed']}}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-lg-12 @if(count($customer->addresses)>0)@else d-lg-none @endif">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex gap-3 overflow-x-auto custom-scrollable">
                            <div class="flex-grow-1 flex-shrink-0 user-select-none">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/total-order.png')}}" alt="">
                                        <h4 class="order-stats__subtitle text-capitalize">{{translate('total_orders')}}</h4>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['total_order']}}</span>
                                </a>
                            </div>
                            <div class="flex-grow-1 flex-shrink-0 user-select-none">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/ongoing.png')}}" alt="">
                                        <h4 class="order-stats__subtitle">{{translate('ongoing')}}</h4>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['ongoing']}}</span>
                                </a>
                            </div>
                            <div class="flex-grow-1 flex-shrink-0 user-select-none">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/completed.png')}}" alt="">
                                        <h4 class="order-stats__subtitle">{{translate('completed')}}</h4>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['completed']}}</span>
                                </a>
                            </div>
                            <div class="flex-grow-1 flex-shrink-0 user-select-none">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/canceled.png')}}" alt="">
                                        <h4 class="order-stats__subtitle">{{translate('canceled')}}</h4>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['canceled']}}</span>
                                </a>
                            </div>
                            <div class="flex-grow-1 flex-shrink-0 user-select-none">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/returned.png')}}" alt="">
                                        <h4 class="order-stats__subtitle">{{translate('returned')}}</h4>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['returned']}}</span>
                                </a>
                            </div>
                            <div class="flex-grow-1 flex-shrink-0 user-select-none">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/failed.png')}}" alt="">
                                        <h4 class="order-stats__subtitle">{{translate('failed')}}</h4>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['failed']}}</span>
                                </a>
                            </div>
                            <div class="flex-grow-1 flex-shrink-0 user-select-none">
                                <a class="order-stats">
                                    <div class="order-stats__content">
                                        <img width="20" src="{{dynamicAsset(path:'public/assets/back-end/img/customer/refunded.png')}}" alt="">
                                        <h4 class="order-stats__subtitle">{{translate('refunded')}}</h4>
                                    </div>
                                    <span class="order-stats__title text--title">{{$orderStatusArray['refunded']}}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <h3 class="card-title m-0">{{translate('orders')}} <span class="badge badge-info text-bg-info">{{$orders->total()}}</span> </h3>
                            <div class="d-flex flex-wrap gap-3">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input id="datatableSearch_" type="search" name="searchValue"
                                                   class="form-control"
                                                   placeholder="{{translate('search_orders')}}" aria-label="Search orders"
                                                   value="{{ request('searchValue') }}"
                                                   >
                                            <div class="input-group-append search-submit">
                                                <button type="submit">
                                                    <i class="fi fi-rr-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <a type="button" class="btn btn-outline-primary text-nowrap" href="{{route('admin.customer.order-list-export',[$customer['id'],'searchValue' => request('searchValue')])}}">
                                    <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}" class="excel" alt="">
                                    <span class="ps-2">{{ translate('export') }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive datatable-custom">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('sl')}}</th>
                                    <th>{{translate('order_ID')}}</th>
                                    <th>{{translate('total')}}</th>
                                    <th>{{translate('order_Status')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $key=>$order)
                                    <tr>
                                        <td>{{$orders->firstItem()+$key}}</td>
                                        <td>
                                            <a href="{{route('admin.orders.details',['id'=>$order['id']])}}"
                                            class="text-dark text-hover-primary">{{$order['id']}}</a>
                                        </td>
                                        <td>
                                            <div class="">
                                                {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['order_amount']))}}
                                            </div>
                                            @if($order->payment_status=='paid')
                                                <span class="badge badge-success text-bg-success">{{translate('paid')}}</span>
                                            @else
                                                <span class="badge badge-danger text-bg-danger">{{translate('unpaid')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order['order_status']=='pending')
                                                <span class="badge badge-info text-bg-info">
                                                    {{translate($order['order_status'])}}
                                                </span>

                                            @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                                                <span class="badge badge-warning text-bg-warning">
                                                        {{str_replace('_',' ',$order['order_status'] == 'processing' ? translate('packaging'):translate($order['order_status']))}}
                                                    </span>
                                            @elseif($order['order_status']=='confirmed')
                                                <span class="badge badge-success text-bg-success">
                                                        {{translate($order['order_status'])}}
                                                    </span>
                                            @elseif($order['order_status']=='failed')
                                                <span class="badge badge-danger text-bg-danger">
                                                        {{translate('failed_to_deliver')}}
                                                    </span>
                                            @elseif($order['order_status']=='delivered')
                                                <span class="badge badge-success text-bg-success">
                                                        {{translate($order['order_status'])}}
                                                    </span>
                                            @else
                                                <span class="badge badge-danger text-bg-danger">
                                                        {{translate($order['order_status'])}}
                                                    </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-10">
                                                <a class="btn btn-outline-primary edit icon-btn" title="{{translate('view')}}" href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                                    <i class="fi fi-rr-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-info icon-btn" title="{{translate('invoice')}}" target="_blank" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
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
                            <div class="d-flex justify-content-end">
                                {!! $orders->links() !!}
                            </div>
                        </div>
                        @if(count($orders)==0)
                            @include('layouts.admin.partials._empty-state',['text'=>'no_order_found'],['image'=>'default'])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        'use strict';

        var swiper = new Swiper(".addressSwiper", {
            slidesPerView: 3,
            spaceBetween: 20,
        });
    </script>

@endpush
