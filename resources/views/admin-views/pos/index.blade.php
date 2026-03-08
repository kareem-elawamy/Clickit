@extends('layouts.admin.app')

@section('title', translate('POS'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="row mt-2">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card">
                    <h4 class="p-3 m-0 bg-light">
                        {{ translate('product_Section') }}
                    </h4>

                    <div class="px-3 py-30">
                        <div class="row gy-1">
                            <div class="col-sm-6">
                                <div class="input-group d-flex justify-content-end">
                                    <select name="category" id="category" class="custom-select w-100 action-category-filter" title="select category">
                                        <option value="">{{ translate('all_categories') }}</option>
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id}}" {{ $categoryId==$item->id?'selected':'' }}>
                                                {{ $item->defaultName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <form action="" method="get">
                                    <div class="input-group flex-grow-1 position-relative">
                                        <input id="search" autocomplete="off" type="text"
                                               value="{{ $searchValue }}"
                                               name="searchValue" class="form-control search-bar-input"
                                               placeholder="{{ translate('search_by_name_or_sku') }}"
                                               aria-label="Search here">
                                        <div class="input-group-append search-submit">
                                            <button type="submit">
                                                <i class="fi fi-rr-search"></i>
                                            </button>
                                        </div>
                                        <diV class="card pos-search-card w-4 position-absolute z-1 w-100 top-40px">
                                            <div id="pos-search-box"
                                                 class="card-body search-result-box d-none"></div>
                                        </diV>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-2 pb-80 overflow-hidden" id="items">
                        @if(count($products) > 0)
                            <div class="pos-item-wrap max-h-100vh-350px">
                                @foreach($products as $product)
                                    @include('admin-views.pos.partials._single-product',['product'=>$product])
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-chat rounded text-center">
                                <div class="py-5">
                                    <img src="http://localhost/Backend-6Valley-eCommerce-CMS/public/assets/back-end/img/empty-product.png" width="64" alt="">
                                    <div class="mx-auto my-3 max-w-353px">
                                        {{ translate('Currently_no_product_available_by_this_name') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="table-responsive bottom-absolute-buttons">
                        <div class="px-4 d-flex justify-content-lg-end">
                            {!!$products->withQueryString()->links()!!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card billing-section-wrap overflow-hidden">
                    <h5 class="p-3 m-0 bg-light">{{ translate('billing_Section') }}</h5>
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2 action-view-all-hold-orders"
                            data-bs-toggle="tooltip" data-bs-title="{{ translate('please_resume_the_order_from_here') }}">
                                {{ translate('view_All_Hold_Orders') }}
                                <span class="total_hold_orders badge text-bg-danger badge-danger rounded-circle fs-10 px-1">
                                    {{ $totalHoldOrder}}
                                </span>
                            </button>
                        </div>

                        <div class="form-group d-flex flex-lg-wrap flex-xl-nowrap gap-2">
                            <?php
                            $userId = 0;
                            if (Illuminate\Support\Str::contains(session('current_user'), 'saved-customer')) {
                                $userId = explode('-', session('current_user'))[2];
                            }
                            ?>
                            <select id='customer' name="customer_id" data-placeholder="Walk-In-Customer" class="js-example-matcher form-control form-ellipsis action-customer-change">
                                <option value="0" {{ $userId == 0 ? 'selected':'' }}>
                                    {{ translate('Walk-In-Customer') }}
                                </option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $userId == $customer->id ? 'selected':'' }}>
                                        {{ $customer->f_name }} {{ $customer->l_name }}
                                        ({{ env('APP_MODE') != 'demo' ? $customer->phone : '+88017'.rand(111, 999).'XXXXX' }})
                                    </option>
                                @endforeach
                            </select>

                            <button class="btn btn-success rounded text-nowrap" id="add_new_customer" type="button"
                                    data-bs-toggle="modal" data-bs-target="#add-customer" title="{{ translate('add_new_customer') }}">
                                {{ translate('add_New_Customer') }}
                            </button>
                        </div>

                        <div id="cart-summary">
                            @include('admin-views.pos.partials._cart-summary')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade pt-5" id="quick-view" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" id="quick-view-modal"></div>
    </div>
</div>

<button class="d-none" id="hold-orders-modal-btn" type="button" data-bs-toggle="modal" data-bs-target="#hold-orders-modal">
</button>

@if($order)
@include('admin-views.pos.partials.modals._print-invoice')
@endif

@include('admin-views.pos.partials.modals._add-customer')
@include('admin-views.pos.partials.modals._hold-orders-modal')
@include('admin-views.pos.partials.modals._add-coupon-discount')
@include('admin-views.pos.partials.modals._add-discount')
@include('admin-views.pos.partials.modals._short-cut-keys')

<span id="route-admin-pos-get-cart-ids" data-url="{{ route('admin.pos.get-cart-ids') }}"></span>
<span id="route-admin-pos-new-cart-id" data-url="{{ route('admin.pos.new-cart-id') }}"></span>
<span id="route-admin-pos-clear-cart-ids" data-url="{{ route('admin.pos.clear-cart-ids') }}"></span>
<span id="route-admin-pos-view-hold-orders" data-url="{{ route('admin.pos.view-hold-orders') }}"></span>
<span id="route-admin-products-search-product" data-url="{{ route('admin.pos.search-product') }}"></span>
<span id="route-admin-pos-change-customer" data-url="{{ route('admin.pos.change-customer') }}"></span>
<span id="route-admin-pos-update-discount" data-url="{{ route('admin.pos.update-discount') }}"></span>
<span id="route-admin-pos-coupon-discount" data-url="{{ route('admin.pos.coupon-discount') }}"></span>
<span id="route-admin-pos-cancel-order" data-url="{{ route('admin.pos.cancel-order') }}"></span>
<span id="route-admin-pos-quick-view" data-url="{{ route('admin.pos.quick-view') }}"></span>
<span id="route-admin-pos-add-to-cart" data-url="{{ route('admin.pos.add-to-cart') }}"></span>
<span id="route-admin-pos-remove-cart" data-url="{{ route('admin.pos.remove-cart') }}"></span>
<span id="route-admin-pos-empty-cart" data-url="{{ route('admin.pos.empty-cart') }}"></span>
<span id="route-admin-pos-update-quantity" data-url="{{ route('admin.pos.update-quantity') }}"></span>
<span id="route-admin-pos-get-variant-price" data-url="{{ route('admin.pos.get-variant-price') }}"></span>
<span id="route-admin-pos-change-cart-editable" data-url="{{ route('admin.pos.change-cart').'/?cart_id=:value' }}"></span>

<span id="message-cart-word" data-text="{{ translate('cart') }}"></span>
<span id="message-stock-out" data-text="{{ translate('stock_out') }}"></span>
<span id="message-stock-id" data-text="{{ translate('in_stock') }}"></span>
<span id="message-add-to-cart" data-text="{{ translate('add_to_cart') }}"></span>
<span id="message-cart-updated" data-text="{{ translate('cart_updated') }}"></span>
<span id="message-update-to-cart" data-text="{{ translate('update_to_cart') }}"></span>
<span id="message-cart-is-empty" data-text="{{ translate('cart_is_empty') }}"></span>
<span id="message-enter-valid-amount" data-text="{{ translate('please_enter_a_valid_amount') }}"></span>
<span id="message-less-than-total-amount" data-text="{{ translate('paid_amount_is_less_than_total_amount') }}"></span>
<span id="message-coupon-is-invalid" data-text="{{ translate('coupon_is_invalid') }}"></span>
<span id="message-product-quantity-updated" data-text="{{ translate('product_quantity_updated') }}"></span>
<span id="message-coupon-added-successfully" data-text="{{ translate('coupon_added_successfully') }}"></span>
<span id="message-sorry-stock-limit-exceeded" data-text="{{ translate('sorry_stock_limit_exceeded') }}"></span>
<span id="message-please-choose-all-the-options" data-text="{{ translate('please_choose_all_the_options') }}"></span>
<span id="message-item-has-been-removed-from-cart" data-text="{{ translate('item_has_been_removed_from_cart') }}"></span>
<span id="message-you-want-to-remove-all-items-from-cart" data-text="{{ translate('you_want_to_remove_all_items_from_cart') }}"></span>
<span id="message-you-want-to-create-new-order" data-text="{{ translate('Want_to_create_new_order_for_another_customer') }}"></span>
<span id="message-product-quantity-is-not-enough" data-text="{{ translate('product_quantity_is_not_enough') }}"></span>
<span id="message-sorry-product-is-out-of-stock" data-text="{{ translate('sorry_product_is_out_of_stock') }}"></span>
<span id="message-item-has-been-added-in-your-cart" data-text="{{ translate('item_has_been_added_in_your_cart') }}"></span>
<span id="message-extra-discount-added-successfully" data-text="{{ translate('extra_discount_added_successfully') }}"></span>
<span id="message-amount-can-not-be-negative-or-zero" data-text="{{ translate('amount_can_not_be_negative_or_zero') }}"></span>
<span id="message-sorry-the-minimum-value-was-reached" data-text="{{ translate('sorry_the_minimum_value_was_reached') }}"></span>
<span id="message-this-discount-is-not-applied-for-this-amount" data-text="{{ translate('this_discount_is_not_applied_for_this_amount') }}"></span>
<span id="message-please-add-product-in-cart-before-applying-discount" data-text="{{ translate('please_add_product_to_cart_before_applying_discount') }}"></span>
<span id="message-please-add-product-in-cart-before-applying-coupon" data-text="{{ translate('please_add_product_to_cart_before_applying_coupon') }}"></span>
<span id="message-product-quantity-cannot-be-zero-in-cart" data-text="{{ translate('product_quantity_can_not_be_zero_or_less_than_zero_in_cart') }}"></span>

@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/printThis/printThis.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/pos-script.js') }}"></script>
    <script>
        "use strict";
        document.addEventListener('DOMContentLoaded', function () {
            @if($order)
            const modalElement = document.getElementById('print-invoice');
            if (modalElement) {
                const modalInstance = new bootstrap.Modal(modalElement);
                modalInstance.show();
            }
            @endif
        });
    </script>

    <script>
        let popupHideTimeout;
        let trackingInterval;

        $(document).on('mouseenter', '.table-items', function () {
            const $popup = $(this).find('.table-items-popup');
            const $item = $(this)[0];

            $('.table-items-popup').not($popup).removeClass('show');
            clearTimeout(popupHideTimeout);
            $popup.addClass('show');

            const updatePopupPosition = () => {
                const rect = $item.getBoundingClientRect();
                $popup.css({
                    top: rect.top + rect.height + 5 + 'px',
                    left: rect.left + (rect.width / 2) - ($popup.outerWidth() / 2) + 'px'
                });
            };

            updatePopupPosition();

            trackingInterval = setInterval(updatePopupPosition, 30);
        });

        $(document).on('mouseleave', '.table-items', function () {
            const $popup = $(this).find('.table-items-popup');
            popupHideTimeout = setTimeout(() => {
                $popup.removeClass('show');
                clearInterval(trackingInterval);
            }, 100);
        });

        $(document).on('mouseenter', '.table-items-popup', function () {
            clearTimeout(popupHideTimeout);
        });

        $(document).on('mouseleave', '.table-items-popup', function () {
            const $popup = $(this);
            popupHideTimeout = setTimeout(() => {
                $popup.removeClass('show');
                clearInterval(trackingInterval);
            }, 100);
        });
    </script>

@endpush
