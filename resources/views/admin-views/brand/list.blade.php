@extends('layouts.admin.app')

@section('title', translate('brand_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/brand.png') }}" alt="">
                {{ translate('brand_List') }}
                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $brands->total() }}</span>
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-20">
                        <div class="d-flex justify-content-between align-items-center gap-20 flex-wrap">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group flex-grow-1 max-w-280">
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                        placeholder="{{ translate('search_by_brand_name') }}"
                                        aria-label="{{ translate('search_by_brand_name') }}"
                                        value="{{ request('searchValue') }}" required>
                                    <div class="input-group-append search-submit">
                                        <button type="submit">
                                            <i class="fi fi-rr-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="dropdown">
                                <a type="button" class="btn btn-outline-primary text-nowrap"
                                    href="{{ route('admin.brand.export', ['searchValue' => request('searchValue')]) }}">
                                    <img width="14"
                                        src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/excel.png') }}"
                                        class="excel" alt="">
                                    <span class="ps-2">{{ translate('export') }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead class="text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('brand_Logo') }}</th>
                                        <th class="max-width-100px">{{ translate('name') }}</th>
                                        <th class="text-center">{{ translate('total_Product') }}</th>
                                        <th class="text-center">{{ translate('total_Order') }}</th>
                                        <th class="text-center">{{ translate('status') }}</th>
                                        <th class="text-center"> {{ translate('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brands as $key => $brand)
                                        <tr>
                                            <td>{{ $brands->firstItem() + $key }}</td>
                                            <td>
                                                <div class="avatar-60 d-flex align-items-center rounded">
                                                    <img class="img-fluid h-100 object-fit-cover w-100" alt=""
                                                        src="{{ getStorageImages(path: $brand->image_full_url, type: 'backend-brand') }}">
                                                </div>
                                            </td>
                                            <td class="overflow-hidden max-w-100px">
                                                <span class="d-inline-block text-truncate w-100" data-bs-toggle="tooltip"
                                                    data-bs-placement="right" aria-label="{{ $brand['defaultname'] }}"
                                                    data-bs-title="{{ $brand['defaultname'] }}">
                                                    {{ $brand['defaultname'] }}
                                                </span>
                                            </td>

                                            <td class="text-center">{{ $brand['brand_all_products_count'] }}</td>
                                            <td class="text-center">
                                                {{ $brand['brandAllProducts']->sum('order_details_count') }}</td>
                                            <td>
                                                <form action="{{ route('admin.brand.status-update') }}" method="post"
                                                    id="brand-status{{ $brand['id'] }}-form" class="no-reload-form">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $brand['id'] }}">
                                                    <label class="switcher mx-auto" for="brand-status{{ $brand['id'] }}">
                                                        <input class="switcher_input custom-modal-plugin" type="checkbox"
                                                            value="1" name="status"
                                                            id="brand-status{{ $brand['id'] }}"
                                                            {{ $brand['status'] == 1 ? 'checked' : '' }}
                                                            data-modal-type="input-change-form"
                                                            data-modal-form="#brand-status{{ $brand['id'] }}-form"
                                                            data-on-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/brand-status-on.png') }}"
                                                            data-off-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/brand-status-off.png') }}"
                                                            data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $brand['defaultname'] . ' ' . translate('status') }}"
                                                            data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $brand['defaultname'] . ' ' . translate('status') }}"
                                                            data-on-message = "<p>{{ translate('if_enabled_this_brand_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                            data-off-message = "<p>{{ translate('if_disabled_this_brand_will_be_hidden_from_the_website_and_customer_app') }}</p>"
                                                            data-on-button-text="{{ translate('turn_on') }}"
                                                            data-off-button-text="{{ translate('turn_off') }}">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-3">
                                                    <a class="btn btn-outline-info icon-btn"
                                                        title="{{ translate('edit') }}"
                                                        href="{{ route('admin.brand.update', [$brand['id']]) }}">
                                                        <i class="fi fi-sr-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-outline-danger icon-btn delete-brand"
                                                        title="{{ translate('delete') }}"
                                                        data-product-count = "{{ count($brand?->brandAllProducts) }}"
                                                        data-text="{{ translate('there_were_') . count($brand?->brandAllProducts) . translate('_products_under_this_brand') . '.' . translate('please_update_their_brand_from_the_below_list_before_deleting_this_one') . '.' }}"
                                                        id="{{ $brand['id'] }}">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4">
                            <div class="d-flex justify-content-lg-end">
                                {{ $brands->links() }}
                            </div>
                        </div>
                        @if (count($brands) == 0)
                            @include(
                                'layouts.admin.partials._empty-state',
                                ['text' => 'no_brand_found'],
                                ['image' => 'default']
                            )
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="route-admin-brand-delete" data-url="{{ route('admin.brand.delete') }}"></span>
    <span id="route-admin-brand-status-update" data-url="{{ route('admin.brand.status-update') }}"></span>
    <span id="get-brands" data-brands="{{ json_encode($brands) }}"></span>
    <div class="modal fade" id="select-brand-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                    <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                        data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body px-4 px-sm-5 pt-0 pb-sm-5">
                    <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
                        <div
                            class="toggle-modal-img-box d-flex flex-column justify-content-center align-items-center mb-3 position-relative">
                            <img src="{{ dynamicAsset('public/assets/new/back-end/img/icons/info.svg') }}" alt=""
                                width="90" />
                        </div>
                        <h5 class="modal-title mb-2 brand-title-message"></h5>
                    </div>
                    <form action="{{ route('admin.brand.delete') }}" method="post"
                        class="product-brand-update-form-submit">
                        @csrf
                        <input name="id" hidden="">
                        <div class="gap-2 mb-3">
                            <label class="form-label" for="exampleFormControlSelect1">{{ translate('select_Category') }}
                                <span class="text-danger">*</span>
                            </label>
                            <select class="custom-select brand-option" name="brand_id"
                                data-placeholder="Select from dropdown" required>
                                <option></option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn-primary min-w-120">{{ translate('update') }}</button>
                            <button type="button" class="btn bg-danger text-danger bg-opacity-10 min-w-120"
                                data-bs-dismiss="modal">{{ translate('cancel') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/products-management.js') }}"></script>
@endpush
