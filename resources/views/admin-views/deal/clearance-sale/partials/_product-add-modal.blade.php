<div class="modal fade" id="product-add-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="border-bottom pb-3">
                    <h3>{{ translate('Add_Product') }}</h3>
                    <p>
                        {{ translate('search_product') }} & {{ translate('add_to_your_clearance_list') }}
                    </p>
                </div>
                <form action="{{route('admin.deal.clearance-sale.add-product')}}" method="post" class="clearance-add-product">
                    @csrf
                    <div class="mt-3">
                        <label class="form-label">{{ translate('Products') }}</label>
                        <div class="dropdown select-clearance-product-search w-100">
                            <div class="search-form" data-bs-toggle="dropdown" aria-expanded="false">
                                <input type="text" class="form-control ps-5 search-product-for-clearance-sale" placeholder="{{ translate('Search_Product') }}" multiple>
                                <span
                                    class="fi fi-rr-search position-absolute inset-inline-start-0 top-0 h-40 d-flex align-items-center ps-2"></span>
                            </div>
                            <div class="dropdown-menu w-100 px-2">
                                <div class="d-flex flex-column max-h-300 overflow-y-auto overflow-x-hidden search-result-box">
                                    @include('admin-views.deal.clearance-sale.partials._search-product', ['products' => $products])
                                </div>
                            </div>
                        </div>
                        <div class="selected-products row g-3 mt-3 clearance-selected-products" id="selected-products">
                            @include('admin-views.partials._select-product')
                        </div>
                    </div>
                    <div class="p-4 bg-section2 rounded text-center mt-3 search-and-add-product">
                        <img src="{{ dynamicAsset('public/assets/back-end/img/empty-product.png') }}" width="64"
                             alt="">
                        <div class="mx-auto my-3 max-w-360">
                            {{ translate('search') }} & {{ translate('and_add_product_from_the_list') }}
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-end mt-3">
                        <button class="btn btn-secondary fw-semibold min-w-120"
                                type="reset" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn btn-primary fw-semibold min-w-120 clearance-product-add-submit" id="add-products-btn"
                                type="button">{{ translate('Add_Products') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
