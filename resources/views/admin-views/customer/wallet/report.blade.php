@extends('layouts.admin.app')

@section('title', translate('customer_wallet'))
@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex justify-content-between flex-wrap gap-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}"
                    alt="">
                {{ translate('wallet') }}
            </h2>
            @if ($customerStatus == 1)
                <button type="button" class="btn btn-primary text-capitalize" data-bs-toggle="modal"
                    data-bs-target="#add-fund-modal">
                    {{ translate('add_fund') }}
                </button>
            @endif
        </div>

        <div class="modal fade" id="add-fund-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between border-0">
                        <h3 class="modal-title text-capitalize" id="exampleModalLongTitle">{{ translate('add_fund') }}</h3>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.customer.wallet.add-fund') }}" method="post"
                            enctype="multipart/form-data" id="add-fund">
                            @csrf
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="form-group d-flex flex-column">
                                        <label class="mb-2 d-flex" for="customer">{{ translate('customer') }}</label>
                                        <select id='form-customer' name="customer_id"
                                            data-placeholder="{{ translate('select_customer') }}"
                                            class="get-customer-list-without-all-customer form-select" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="mb-2 d-flex" for="amount">{{ translate('amount') }}</label>
                                        <input type="number" class="form-control" name="amount" id="amount"
                                            step=".01" placeholder="{{ translate('ex') . ':' . '500' }}" required>
                                        <small id="amount_error" style="color: red; display: none;">Amount cannot be zero or
                                            negative</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="mb-2 d-flex align-items-center gap-1"
                                            for="reference">{{ translate('reference') }}
                                            <small>({{ translate('optional') }})</small></label>
                                        <input type="text" class="form-control" name="reference"
                                            placeholder="{{ translate('ex') . ':' . 'abc990' }}" id="reference">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">{{ translate('close') }}</button>
                                <button type="submit" id="submit"
                                    class="btn btn-primary">{{ translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header text-capitalize">
                <h3 class="mb-0">{{ translate('filter_options') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 pt-3">
                        <form action="{{ route('admin.customer.wallet.report') }}" method="get">
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <input type="date" name="from" id="start-date-time"
                                            value="{{ request()->get('from') }}" class="form-control"
                                            title="{{ ucfirst(translate('from_date')) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <input type="date" name="to" id="end-date-time"
                                            value="{{ request()->get('to') }}" class="form-control"
                                            title="{{ ucfirst(translate('to_date')) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        @php
                                            $transaction_status = request()->get('transaction_type');
                                        @endphp
                                        <div class="select-wrapper">
                                            <select name="transaction_type" class="form-select"
                                                title="{{ translate('select_transaction_type') }}">
                                                <option value="">{{ translate('all') }}</option>
                                                <option value="add_fund_by_admin"
                                                    {{ isset($transaction_status) && $transaction_status == 'add_fund_by_admin' ? 'selected' : '' }}>
                                                    {{ translate('add_fund_by_admin') }}</option>
                                                <option value="add_fund"
                                                    {{ isset($transaction_status) && $transaction_status == 'add_fund' ? 'selected' : '' }}>
                                                    {{ translate('add_fund') }}</option>
                                                <option value="order_refund"
                                                    {{ isset($transaction_status) && $transaction_status == 'order_refund' ? 'selected' : '' }}>
                                                    {{ translate('refund_order') }}</option>
                                                <option value="loyalty_point"
                                                    {{ isset($transaction_status) && $transaction_status == 'loyalty_point' ? 'selected' : '' }}>
                                                    {{ translate('customer_loyalty_point') }}</option>
                                                <option value="order_place"
                                                    {{ isset($transaction_status) && $transaction_status == 'order_place' ? 'selected' : '' }}>
                                                    {{ translate('order_place') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <input type="hidden" id='customer-id' name="customer_id"
                                            value="{{ request('customer_id') ?? 'all' }}">
                                        <select
                                            data-placeholder="
                                                    @if ($customer == 'all') {{ translate('all_customer') }}
                                                    @else
                                                        {{ $customer['name'] ?? $customer['f_name'] . ' ' . $customer['l_name'] . ' ' . '(' . $customer['phone'] . ')' }} @endif"
                                            class="get-customer-list-by-ajax-request form-control form-ellipsis set-customer-value form-select">
                                            <option value="all">{{ translate('all_customer') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ translate('filter') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div class="card mt-3">
            <div class="card-header text-capitalize">
                <h3 class="mb-0">{{ translate('summary') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $credit = $data[0]->total_credit;
                        $debit = $data[0]->total_debit;
                        $balance = $credit - $debit;
                    @endphp
                    <div class="order-stats flex-grow-1">
                        <div class="order-stats__content">
                            <i class="fi fi-rr-deposit"></i>
                            <h4 class="order-stats__subtitle">{{ translate('debit') }}</h4>
                        </div>
                        <span class="order-stats__title text-primary">
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $debit ?? 0)) }}
                        </span>
                    </div>
                    <div class="order-stats flex-grow-1">
                        <div class="order-stats__content">
                            <i class="fi fi-rr-sack-dollar"></i>
                            <h4 class="order-stats__subtitle">{{ translate('credit') }}</h4>
                        </div>
                        <span class="order-stats__title text-warning">
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $credit ?? 0)) }}
                        </span>
                    </div>
                    <div class="order-stats flex-grow-1">
                        <div class="order-stats__content">
                            <i class="fi fi-rr-wallet"></i>
                            <h4 class="order-stats__subtitle">{{ translate('balance') }}</h4>
                        </div>
                        <span class="order-stats__title text-success">
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $balance ?? 0)) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between gap-3 align-items-center mb-4">
                    <h3 class="mb-0 text-nowrap text-capitalize d-flex gap-1 align-items-center">
                        {{ translate('transactions') }}
                        <span class="badge badge-info text-bg-info">{{ $transactions->total() }}</span>
                    </h3>

                    <a type="button" class="btn btn-outline-primary text-nowrap"
                        href="{{ route('admin.customer.wallet.export', ['transaction_type' => $transaction_status, 'customer_id' => request('customer_id'), 'to' => request('to'), 'from' => request('from')]) }}">
                        <img width="14" src="{{ dynamicAsset(path: 'public/assets/back-end/img/excel.png') }}"
                            class="excel" alt="">
                        <span class="ps-2">{{ translate('export') }}</span>
                    </a>
                </div>

                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('transaction_ID') }}</th>
                                <th>{{ translate('Customer') }}</th>
                                <th>{{ translate('credit') }}</th>
                                <th>{{ translate('debit') }}</th>
                                <th>{{ translate('balance') }}</th>
                                <th>{{ translate('transaction_type') }}</th>
                                <th>{{ translate('reference') }}</th>
                                <th class="text-center">{{ translate('created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $key => $transaction)
                                <tr>
                                    <td>{{ $transactions->firstItem() + $key }}</td>
                                    <td>{{ $transaction['transaction_id'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer.view', ['user_id' => $transaction['user_id']]) }}"
                                            class="text-dark text-hover-primary">{{ Str::limit($transaction['user'] ? $transaction?->user->f_name . ' ' . $transaction?->user->l_name : translate('not_found'), 20) }}</a>
                                    </td>
                                    <td>
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction['credit'])) }}
                                        @if ($transaction['transaction_type'] == 'add_fund' && $transaction['admin_bonus'] > 0)
                                            <span class="text-sm badge badge-soft-success">
                                                +
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction['admin_bonus'])) }}
                                                {{ translate('admin_bonus') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction['debit'])) }}
                                    </td>

                                    <td>
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction['balance'])) }}
                                    </td>

                                    <td>
                                        <span
                                            class="badge badge-soft-{{ $transaction['transaction_type'] == 'order_refund'
                                                ? 'danger'
                                                : ($transaction['transaction_type'] == 'loyalty_point'
                                                    ? 'warning'
                                                    : ($transaction['transaction_type'] == 'order_place'
                                                        ? 'info'
                                                        : 'success')) }}">
                                            {{ translate($transaction['transaction_type']) }}
                                        </span>
                                    </td>
                                    <td>{{ translate(str_replace('_', ' ', $transaction['reference'])) }}</td>
                                    <td class="text-center">
                                        {{ date('Y/m/d ' . config('timeformat'), strtotime($transaction['created_at'])) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-lg-end">
                        {!! $transactions->links() !!}
                    </div>
                </div>
                @if (count($transactions) == 0)
                    @include(
                        'layouts.admin.partials._empty-state',
                        ['text' => 'no_data_found'],
                        ['image' => 'default']
                    )
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';

        let errorTimeout;

        $("#amount").on('input', function() {
            const value = parseFloat($(this).val());
            if (isNaN(value) || value <= 0) {
                $(this).val('');
                $("#amount_error").fadeIn(200);
                clearTimeout(errorTimeout);
                errorTimeout = setTimeout(function() {
                    $("#amount_error").fadeOut(500);
                }, 1000);
            } else {
                $("#amount_error").fadeOut(300);
            }
        });



        $('#add-fund').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            Swal.fire({
                title: "{{ translate('are_you_sure') . '?' }} ",
                text: '{{ translate('you_want_to_add_fund') }} ' + $('#amount').val() +
                    ' {{ getCurrencyCode(type: 'default') . ' ' . translate('to') }} ' + $(
                        '#form-customer option:selected').text() + '{{ translate('to_wallet') }}',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: '#dd3333',
                cancelButtonText: '{{ translate('no') }}',
                confirmButtonText: '{{ translate('add') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.post({
                        url: '{{ route('admin.customer.wallet.add-fund') }}',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            if (data.errors) {
                                for (let i = 0; i < data.errors.length; i++) {
                                    setTimeout(() => {
                                        toastMagic.error(data.errors[i].message);
                                    }, index * 500);
                                }
                            } else {
                                toastMagic.success(
                                    '{{ translate('fund_added_successfully') }}');
                                setTimeout(() => {
                                    location.reload()
                                }, 1500);
                            }
                        }
                    });
                }
            })
        })
    </script>
@endpush
