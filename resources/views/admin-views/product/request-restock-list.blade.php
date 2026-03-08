@extends('layouts.admin.app')

@section('title', translate('restock_product_List'))

@section('content')
    <div class="content container-fluid">

        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('Request_Restock_List') }}
                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $totalRestockProducts }}</span>
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET">
                    <input type="hidden" value="{{ request('status') }}" name="status">
                    <div class="row g-2">
                        <div class="col-12">
                            <h3 class="mb-3">{{ translate('filter_Products') }}</h3>
                        </div>
                        @if (request('type') == 'seller')
                            <div class="col-sm-6 col-lg-6 col-xxl-3">
                                <div class="form-group">
                                    <label class="form-label" for="store">{{ translate('store') }}</label>
                                    <select name="seller_id" class="custom-select">
                                        <option value="" selected>{{ translate('all_store') }}</option>
                                        @foreach ($sellers as $seller)
                                            <option
                                                value="{{ $seller->id }}"{{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                                {{ $seller->shop->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-6 col-lg-6 col-xxl-3">
                            <div class="form-group">
                                <label class="form-label" for="store">{{ translate('Request_Restock_Date') }}</label>
                                <div class="position-relative">
                                    <span class="fi fi-sr-calendar icon-absolute-on-right"></span>
                                    <input type="text"
                                        class="js-daterangepicker previous-date-true placeholder-mode-true form-control"
                                        name="restock_date" placeholder="{{ translate('Select_Date') }}"
                                        value="{{ request('restock_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-6 col-xxl-3">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ translate('category') }}</label>
                                <select class="custom-select action-get-request-onchange" name="category_id"
                                    data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"
                                    data-element-id="sub-category-select" data-element-type="select">
                                    <option value="{{ old('category_id') }}" selected disabled>
                                        {{ translate('select_category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ request('category_id') == $category['id'] ? 'selected' : '' }}>
                                            {{ $category['defaultName'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-6 col-xxl-3">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ translate('sub_Category') }}</label>
                                <select class="custom-select action-get-request-onchange" name="sub_category_id"
                                    id="sub-category-select"
                                    data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"
                                    data-element-id="sub-sub-category-select" data-element-type="select">
                                    <option disabled {{ request('sub_category_id') ? '' : 'selected' }}>
                                        {{ translate('select_Sub_Category') }}
                                    </option>
                                    @foreach ($subCategories as $subCategoryItem)
                                        <option value="{{ $subCategoryItem['id'] }}"
                                            {{ request('sub_category_id') == $subCategoryItem['id'] ? 'selected' : '' }}>
                                            {{ $subCategoryItem['defaultName'] }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-6 col-xxl-3">
                            <div class="form-group">
                                <label class="form-label" for="store">{{ translate('brand') }}</label>
                                <select name="brand_id" class="custom-select">
                                    <option value="" selected>{{ translate('select_brand') }}</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->default_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-end mt-4">
                                <a href="{{ route('admin.products.request-restock-list') }}"
                                    class="btn btn-secondary px-4">
                                    {{ translate('reset') }}
                                </a>
                                <button type="submit" class="btn btn-primary px-4 action-get-element-type">
                                    {{ translate('show_data') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-20">
            <div class="card">
                <div class="px-3 py-4 d-flex justify-content-between align-items-center gap-20 flex-wrap">
                    <h3 class="mb-0">
                        {{ translate('request_list') }}
                        <span
                            class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $restockProducts->total() }}</span>
                    </h3>
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-sm-end flex-grow-1">
                        <div class="flex-grow-1 max-w-280">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group flex-grow-1 max-w-280">
                                    <input type="hidden" name="restock_date" value="{{ request('restock_date') }}">
                                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                                    <input type="hidden" name="sub_category_id" value="{{ request('sub_category_id') }}">
                                    <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                        placeholder="{{ translate('search_by_Product_Name') }}" aria-label="Search orders"
                                        value="{{ request('searchValue') }}">
                                    <div class="input-group-append search-submit">
                                        <button type="submit">
                                            <i class="fi fi-rr-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="dropdown">
                            <a type="button" class="btn btn-outline-primary text-nowrap"
                                href="{{ route('admin.products.restock-export', ['restock_date' => request('restock_date'), 'brand_id' => request('brand_id'), 'category_id' => request('category_id'), 'sub_category_id' => request('sub_category_id'), 'searchValue' => request('searchValue')]) }}">
                                <img width="14"
                                    src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/excel.png') }}"
                                    class="excel" alt="">
                                <span class="ps-2">{{ translate('export') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered align-middle">
                        <thead class="text-capitalize">
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('product_name') }}</th>
                                <th class="text-center">{{ translate('selling_price') }}</th>
                                <th class="text-center">{{ translate('last_request_date') }}</th>
                                <th class="text-center">{{ translate('number_of_request') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($restockProducts as $key => $restockProduct)
                                <tr>
                                    <th scope="row"> {{ $restockProducts->firstItem() + $key }}</th>
                                    <td>
                                        <a href="{{ route('admin.products.view', ['addedBy' => $restockProduct->product['added_by'] == 'seller' ? 'vendor' : 'in-house', 'id' => $restockProduct->product['id'] ?? 0]) }}"
                                            class="media align-items-center gap-2">
                                            <img src="{{ getStorageImages(path: $restockProduct?->product?->thumbnail_full_url, type: 'backend-product') }}"
                                                class="avatar border object-fit-cover" alt="">
                                            <span class="media-body text-dark text-primary-hover">
                                                {{ Str::limit($restockProduct->product['name'] ?? '', 20) }}
                                                <p class="small fw-bold m-0">
                                                    @if ($restockProduct['variant'])
                                                        {{ translate('Variant:') . ' ' . $restockProduct['variant'] }}
                                                    @endif
                                                </p>
                                            </span>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $restockProduct->product['unit_price'] ?? 0), currencyCode: getCurrencyCode()) }}
                                    </td>
                                    <td class="text-center">
                                        {{ $restockProduct->updated_at->format('d F Y, h:i A') }}
                                    </td>
                                    <td class="text-center">
                                        {{ $restockProduct?->restockProductCustomers?->count() ?? 0 }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info icon-btn" title="View"
                                                href="{{ route('admin.products.view', ['addedBy' => $restockProduct->product['added_by'] == 'seller' ? 'vendor' : 'in-house', 'id' => $restockProduct->product['id'] ?? 0]) }}">
                                                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/icons/restock_view.svg') }}"
                                                    alt="">
                                            </a>
                                            <a class="btn btn-outline-primary icon-btn action-update-product-quantity"
                                                title="{{ translate('edit') }}"
                                                id="{{ $restockProduct->product['id'] }}"
                                                data-url="{{ route('admin.products.get-variations', ['id' => $restockProduct->product['id'], 'restock_id' => $restockProduct->id]) }}"
                                                data-bs-target="#update-stock">
                                                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/icons/restock_update.svg') }}"
                                                    alt="">
                                            </a>
                                            <span class="btn btn-outline-danger icon-btn delete-data"
                                                title="{{ translate('delete') }}"
                                                data-id="product-{{ $restockProduct->id }}">
                                                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/icons/restock_delete.svg') }}"
                                                    alt="">
                                            </span>
                                        </div>
                                        <form action="{{ route('admin.products.restock-delete', [$restockProduct->id]) }}"
                                            method="post" id="product-{{ $restockProduct->id }}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-lg-end">
                        {{ $restockProducts->links() }}
                    </div>
                </div>

                @if (count($restockProducts) == 0)
                    @include(
                        'layouts.admin.partials._empty-state',
                        ['text' => 'no_product_found'],
                        ['image' => 'default']
                    )
                @endif
            </div>
        </div>
    </div>
    <span id="message-select-word" data-text="{{ translate('select') }}"></span>
    <div class="modal fade update-stock-modal restock-stock-update" id="update-stock" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.products.update-quantity') }}" method="post" class="odal-body p-20">
                    @csrf
                    <div class="rest-part-content"></div>
                    <div class="d-flex justify-content-end gap-10 flex-wrap align-items-center">
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal" aria-label="Close">
                            {{ translate('close') }}
                        </button>
                        <button class="btn btn-primary px-4" type="submit">
                            {{ translate('update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
