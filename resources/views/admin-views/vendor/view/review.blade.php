@extends('layouts.admin.app')

@section('title', $seller?->shop->name ?? translate("shop_name_not_found"))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2 align-items-center">
                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png')}}" alt="">
                {{translate('vendor_details')}}
            </h2>
        </div>

        <div class="flex-between d-sm-flex row align-items-center justify-content-between mb-2 mx-1">
            <div>
                @if ($seller->status=="pending")
                    <div class="mt-4 pe-2">
                        <div class="flex-between">
                            <div class="mx-1"><h4><i class="fi fi-rr-shop"></i></h4></div>
                            <div><h4>{{translate('vendor_request_for_open_a_shop.')}}</h4></div>
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
            <h2 class="page-header-title mb-3">{{ $seller?->shop->name ?? translate("shop_Name")." : ".translate("update_Please") }}</h2>

            <div class="position-relative nav--tab-wrapper">
                <ul class="nav nav-pills nav--tab">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vendors.view',$seller->id) }}">{{translate('shop')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'order']) }}">{{translate('order')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'product']) }}">{{translate('product')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vendors.view',['id'=>$seller['id'], 'tab'=>'clearance_sale']) }}">{{translate('clearance_sale_products')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'setting']) }}">{{translate('setting')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'transaction']) }}">{{translate('transaction')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.vendors.view',['id'=>$seller->id, 'tab'=>'review']) }}">{{translate('review')}}</a>
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

        <div class="content container-fluid p-0">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-sm-4 col-md-6 col-lg-8 mb-3 mb-sm-0">
                            <h3 class="mb-0 d-flex gap-1 align-items-center">
                                {{translate('review_table') }}
                                <span class="badge badge-info text-bg-info">{{ $reviews->total() }}</span>
                            </h3>
                        </div>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="searchValue"
                                           class="form-control"
                                           placeholder="{{translate('search_by_product_name')}}" aria-label="Search orders"
                                           value="{{ request('searchValue') }}">
                                    <div class="input-group-append search-submit">
                                        <button type="submit">
                                            <i class="fi fi-rr-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable" class="table table-hover table-borderless table-thead-bordered table-nowrap align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('product')}}</th>
                                    <th>{{translate('review')}}</th>
                                    <th>{{translate('rating')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($reviews as $key=> $review)
                                <tr>
                                    <td>{{$reviews->firstItem()+ $key}}</td>
                                    <td>
                                        <div class="min-w-300">
                                            @if($review->product)
                                                <a href="{{route('admin.products.view',['addedBy'=>($review->product->added_by =='seller'?'vendor' : 'in-house'),'id'=>$review->product->id])}}"
                                                    class="line-2 max-w-250 text-dark text-hover-primary word-break">
                                                    {{$review->product['name']}}
                                                </a>
                                            @else
                                                <a href="javascript:" class="text-dark text-hover-primary">
                                                    {{ translate('product_not_found') }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-wrap mb-1">
                                            {{$review->comment ?? translate("no_Comment_Found")}}
                                        </p>
                                        @if(count($review->attachment_full_url)>0)
                                           <div class="d-flex flex-wrap gap-1 max-w-200 min-w-200">
                                                @foreach ($review->attachment_full_url as $img)
                                                    <a href="{{ getStorageImages(path:$img, type: 'backend-basic') }}" data-lightbox="mygallery" class="flex-shrink-0">
                                                        <img class="p-1" width="60" height="60" src="{{ getStorageImages(path:$img, type: 'backend-basic') }}" alt="" >
                                                    </a>
                                                @endforeach
                                           </div>
                                        @endif
                                    </td>
                                    <td>
                                        <label class="badge badge-info text-bg-info">
                                            {{$review->rating }} <i class="fi fi-sr-star"></i>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-end">
                            {{$reviews->links()}}
                        </div>
                    </div>

                    @if(count($reviews)==0)
                        @include('layouts.admin.partials._empty-state',['text'=>'no_review_found'],['image'=>'default'])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
