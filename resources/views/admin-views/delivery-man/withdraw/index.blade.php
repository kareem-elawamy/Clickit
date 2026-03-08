@extends('layouts.admin.app')

@section('title', translate('withdraw_Request'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png')}}" alt="">
                {{translate('withdraw_Request')}}
            </h2>
        </div>

        <div class="card">
            <div class="px-3 py-4">
                <div class="row align-items-center">
                    <div class="col-lg-4">
                        <h3>
                            {{ translate('withdraw_Request_Table')}}
                            <span  class="badge badge-info text-bg-info" id="withdraw-requests-count">{{ $withdrawRequests->total() }}</span>
                        </h3>
                    </div>
                    <div class="col-lg-8 mt-3 mt-lg-0 d-flex gap-3 justify-content-lg-end flex-wrap">
                        <div class="">
                            <form action="{{ url()->current() }}" method="GET">
                                {{-- <div class="input-group input-group-merge input-group-custom">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input type="search" name="searchValue" class="form-control"
                                            placeholder="{{translate('search_by_name')}}"
                                            value="{{ request('searchValue') }}">
                                    <button type="submit" class="btn btn--primary">{{translate('search')}}</button>
                                </div> --}}
                                <div class="input-group">
                                    <input type="search" name="order_search" class="form-control" placeholder="{{translate('search_by_name')}}" value="{{ request('order_search') ?? '' }}">
                                    <div class="input-group-append search-submit">
                                        <button type="submit">
                                            <i class="fi fi-rr-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <a type="button" class="btn btn-outline-primary text-nowrap" href="{{route('admin.delivery-man.withdraw-list-export',['searchValue'=> request('searchValue')??''])}}">
                            <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}" class="excel" alt="">
                            <span class="ps-2">{{ translate('export') }}</span>
                        </a>

                        <div class="min-w-200">
                            <select name="status" class="custom-select form-select status-filter">
                                <option value="all" {{ request('approved') == 'all'?'selected':''}}>{{translate('all')}}</option>
                                <option value="approved" {{ request('approved') == 'approved' ?'selected':''}}>{{translate('approved')}}</option>
                                <option value="denied" {{ request('approved') == 'denied'?'selected':''}}>{{translate('denied')}}</option>
                                <option value="pending" {{ request('approved') == 'pending'?'selected':''}}>{{translate('pending')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div id="status-wise-view">
                @include('admin-views.delivery-man.withdraw._table')
            </div>
            <div class="table-responsive mt-4">
                <div class="px-4 d-flex justify-content-center justify-content-end">
                    {{$withdrawRequests->links()}}
                </div>
            </div>
        </div>
    </div>

    {{-- Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasWithdraw" aria-labelledby="offcanvasWithdrawLabel">
        <div class="offcanvas-header bg-section">
            <h3 class="mb-0">{{ translate('Withdraw_Information') }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <div class="withdraw-info-sidebar-wrap withdraw-details-view">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <span id="get-status-filter-route" data-action="{{ route('admin.delivery-man.withdraw-list',['searchValue'=> request('searchValue')]) }}"></span>

@endsection
@push('script')
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/admin/withdraw.js')}}"></script>
@endpush
