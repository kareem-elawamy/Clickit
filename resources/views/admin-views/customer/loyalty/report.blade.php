@extends('layouts.admin.app')

@section('title', translate('customer_loyalty_point_report'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/loyalty_point.png')}}" alt="">
                {{translate('customer_loyalty_point_report')}}
            </h2>
        </div>
        <div class="card">
            <div class="card-header text-capitalize">
                <h3 class="mb-0">{{translate('filter_options')}}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 pt-3">
                        <form action="{{route('admin.customer.loyalty.report')}}" method="get">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <input type="date" name="from" id="start-date-time" value="{{request()->get('from')}}" class="form-control" title="{{ucfirst(translate('from_date'))}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <input type="date" name="to" id="end-date-time" value="{{request()->get('to')}}" class="form-control" title="{{ucfirst(translate('to_date'))}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        @php
                                        $transaction_status=request()->get('transaction_type');
                                        @endphp
                                        <div class="select-wrapper">
                                            <select name="transaction_type" id="" class="form-select" title="{{translate('select_transaction_type')}}">
                                                <option value="">{{ translate('all')}}</option>
                                                <option value="point_to_wallet" {{ isset($transaction_status) && $transaction_status=='point_to_wallet'?'selected':''}}>{{ translate('point_to_wallet')}}</option>
                                                <option value="order_place" {{ isset($transaction_status) && $transaction_status=='order_place'?'selected':''}}>{{ translate('order_place')}}</option>
                                                <option value="refund_order" {{ isset($transaction_status) && $transaction_status=='refund_order'?'selected':''}}>{{ translate('refund_order')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <input type="hidden" id='customer-id'  name="customer_id" value="{{request('customer_id') ?? 'all'}}">
                                        <select data-placeholder="
                                                    @if($customer == 'all')
                                                        {{translate('all_customer')}}
                                                    @else
                                                        {{ $customer['name'] ?? $customer['f_name'].' '.$customer['l_name'].' '.'('.$customer['phone'].')'}}
                                                    @endif"
                                                class="get-customer-list-by-ajax-request form-select form-ellipsis set-customer-value">
                                            <option value="all">{{translate('all_customer')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary"><i class="fi fi-rr-filter"></i>{{translate('filter')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header text-capitalize">
                <h3 class="mb-0">{{translate('summary')}}</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $credit = $data[0]->total_credit??0;
                        $debit = $data[0]->total_debit??0;
                        $balance = $credit - $debit;
                    @endphp
                    <div class="order-stats flex-grow-1">
                        <div class="order-stats__content">
                            <i class="fi fi-rr-deposit"></i>
                            <h4 class="order-stats__subtitle">{{translate('debit')}}</h4>
                        </div>
                        <span class="order-stats__title fz-14 text--primary">
                            {{ (int)($debit)}}
                        </span>
                    </div>
                    <div class="order-stats flex-grow-1">
                        <div class="order-stats__content">
                            <i class="fi fi-rr-sack-dollar"></i>
                            <h4 class="order-stats__subtitle">{{translate('credit')}}</h4>
                        </div>
                        <span class="order-stats__title fz-14 text-warning">
                            {{ (int)$credit}}
                        </span>
                    </div>
                    <div class="order-stats flex-grow-1">
                        <div class="order-stats__content">
                            <i class="fi fi-rr-wallet-arrow"></i>
                            <h4 class="order-stats__subtitle">{{translate('balance')}}</h4>
                        </div>
                        <span class="order-stats__title fz-14 text-success">
                            {{ (int)$balance}}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center text-capitalize gap-2 mb-4">
                    <h3 class="mb-0 text-nowrap ">
                        {{translate('transactions')}}
                        <span class="badge badge-info text-bg-info">{{$transactions->total()}}</span>
                    </h3>

                    <a type="button" class="btn btn-outline-primary text-nowrap" href="{{route('admin.customer.loyalty.export',['transaction_type'=>$transaction_status,'customer_id'=>request('customer_id'),'to'=>request('to'),'from'=>request('from')])}}">
                        <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}" class="excel" alt="">
                        <span class="ps-2">{{ translate('export') }}</span>
                    </a>
                </div>
                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table {{Session::get('direction') === "rtl" ? 'text-right' : 'text-left'}}">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('transaction_ID')}}</th>
                                <th>{{translate('customer')}}</th>
                                <th>{{translate('credit')}}</th>
                                <th>{{translate('debit')}}</th>
                                <th>{{translate('balance')}}</th>
                                <th>{{translate('transaction_type')}}</th>
                                <th>{{translate('reference')}}</th>
                                <th class="text-center">{{translate('created_at')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $key=>$transaction)
                            <tr scope="row">
                                <td >{{$key+$transactions->firstItem()}}</td>
                                <td>{{$transaction['transaction_id']}}</td>
                                <td><a href="{{route('admin.customer.view',['user_id'=>$transaction['user_id']])}}" class="text-dark text-hover-primary">{{Str::limit($transaction->user?$transaction->user->f_name.' '.$transaction->user->l_name:translate('not_found'),20)}}</a></td>
                                <td>{{$transaction['credit']}}</td>
                                <td>{{$transaction['debit']}}</td>
                                <td>{{$transaction['balance']}}</td>
                                <td>
                                    <span class="badge badge-{{$transaction['transaction_type']=='order_refund'
                                        ?'danger'
                                        :($transaction['transaction_type']=='loyalty_point'?'warning'
                                            :($transaction['transaction_type']=='order_place'
                                                ?'info'
                                                :'success'))
                                        }} text-bg-{{$transaction['transaction_type']=='order_refund'
                                        ?'danger'
                                        :($transaction['transaction_type']=='loyalty_point'?'warning'
                                            :($transaction['transaction_type']=='order_place'
                                                ?'info'
                                                :'success'))
                                        }}">
                                        {{translate($transaction['transaction_type'])}}
                                    </span>
                                </td>
                                <td>{{$transaction['reference']}}</td>
                                <td class="text-center">{{date('Y/m/d '.config('timeformat'), strtotime($transaction['created_at']))}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>


                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-lg-end">
                        {!!$transactions->links()!!}
                    </div>
                </div>
                @if(count($transactions)==0)
                    @include('layouts.admin.partials._empty-state',['text'=>'no_data_found'],['image'=>'default'])
                @endif
            </div>
        </div>
    </div>
@endsection
