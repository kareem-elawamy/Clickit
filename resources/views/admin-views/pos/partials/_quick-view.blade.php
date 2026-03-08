<div class="modal-header border-0 pb-0 d-flex justify-content-end">
    <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none" data-bs-dismiss="modal"
        aria-label="Close">
    </button>
</div>
<div class="modal-body">
    <div class="row gy-3">
        <div class="col-md-5">
            <div class="d-flex align-items-center justify-content-center active">
                <img class="img-responsive w-100 rounded aspect-1"
                    src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}"
                    data-zoom="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}"
                    alt="{{ translate('product_image') }}">
                <div class="cz-image-zoom-pane"></div>
            </div>

            <div class="d-flex flex-column gap-10 fs-14 mt-3">

                <div class="d-flex align-items-center gap-2">
                    <div class="fw-bold text-dark">{{ translate('SKU') }}:</div>
                    <div>{{ $product->code }}</div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="fw-bold text-dark">{{ translate('categories') }}: </div>
                    <div>{{ $product->category->name ?? translate('not_found') }}</div>
                </div>

                @if ($product->product_type == 'physical')
                    <div class="d-flex align-items-center gap-2">
                        <div class="fw-bold text-dark">{{ translate('brand') }}:</div>
                        <div>{{ $product?->brand?->name ?? translate('not_found') }}</div>
                    </div>
                @endif

                @if (count($product->tags) > 0)
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="fw-bold text-dark">{{ translate('tag') }}:</div>
                        @foreach ($product->tags as $tag)
                            <div>{{ $tag->tag }},</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-7">

            <div class="mt-3">
                <form id="add-to-cart-form" class="add-to-cart-details-form">
                    @csrf
                    <div class="details">
                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <div
                                class="d-flex gap-2 align-items-center text-success rounded-pill bg-success bg-opacity-10 px-2 py-1 stock-status-in-quick-view">
                                <i class="fi fi-rr-check-circle"></i>
                                {{ translate('in_stock') }}
                            </div>
                        </div>
                        <h2 class="mb-3 product-title">{{ $product->name }}</h2>
                        @if ($product->reviews_count > 0)
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fi fi-sr-star text-warning"></i>
                                <span
                                    class="text-muted text-capitalize">({{ $product->reviews_count . ' ' . translate('customer_review') }})</span>
                            </div>
                        @endif
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2 text-dark">
                            <h2
                                class="text-primary text-accent price-range-with-discount d-flex gap-2 align-items-center mb-0">
                                <span class="discounted-unit-price fs-24 fw-bold">
                                    {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                                </span>
                                @if (getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                    <del class="product-total-unit-price align-middle text-muted fs-18 fw-semibold">
                                        {{ webCurrencyConverter(amount: $product->unit_price) }}
                                    </del>
                                @endif
                            </h2>
                            <div class="align-self-center discounted-badge-element">
                                @if (getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                    <div
                                        class="d-flex gap-1 align-items-center text-primary rounded-pill bg-primary-light px-2 py-1">
                                        <span class="set-discount-amount discounted_badge fz-12">
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <?php
                    $cart = false;
                    if (session()->has('cart')) {
                        foreach (session()->get('cart') as $key => $cartItem) {
                            if (is_array($cartItem) && $cartItem['id'] == $product['id']) {
                                $cart = $cartItem;
                            }
                        }
                    }
                    
                    ?>

                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <div class="variant-change">
                        <div class="position-relative mb-4">
                            @if (count(json_decode($product->colors)) > 0)
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <strong class="text-dark">{{ translate('color') }}</strong>

                                    <div class="color-select d-flex gap-2 flex-wrap" id="option1">
                                        @foreach (json_decode($product->colors) as $key => $color)
                                            <input class="btn-check action-color-change" type="radio"
                                                id="{{ $product->id }}-color-{{ $key }}" name="color"
                                                value="{{ $color }}"
                                                @if ($key == 0) checked @endif autocomplete="off">
                                            <label id="label-{{ $product->id }}-color-{{ $key }}"
                                                class="color-ball mb-0 {{ $key == 0 ? 'border-add' : '' }}"
                                                style="background: {{ $color }};"
                                                for="{{ $product->id }}-color-{{ $key }}"
                                                data-bs-toggle="tooltip">
                                                <i class="fi fi-sr-check"></i>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @php
                                $qty = 0;
                                if (!empty($product->variation)) {
                                    foreach (json_decode($product->variation) as $key => $variation) {
                                        $qty += $variation->qty;
                                    }
                                }
                            @endphp
                        </div>

                        @foreach (json_decode($product->choice_options) as $key => $choice)
                            <div class="d-flex gap-3 flex-wrap align-items-center mb-3">
                                <div class="my-2 w-43px">
                                    <strong class="text-dark">{{ ucfirst($choice->title) }}</strong>
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach ($choice->options as $index => $option)
                                        <input class="btn-check" type="radio"
                                            id="{{ $choice->name }}-{{ $option }}" name="{{ $choice->name }}"
                                            value="{{ $option }}"
                                            @if ($index == 0) checked @endif autocomplete="off">
                                        <label class="btn btn-sm check-label border-0 mb-0 w-auto pos-check-label"
                                            for="{{ $choice->name }}-{{ $option }}">{{ $option }}</label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @php($extensionIndex = 0)
                        @if (
                            $product['product_type'] == 'digital' &&
                                $product['digital_product_file_types'] &&
                                count($product['digital_product_file_types']) > 0 &&
                                $product['digital_product_extensions']
                        )
                            @foreach ($product['digital_product_extensions'] as $extensionKey => $extensionGroup)
                                <div class="d-flex gap-3 flex-wrap align-items-center mb-3">
                                    <div class="my-2">
                                        <strong class="text-dark">{{ translate($extensionKey) }} :</strong>
                                    </div>

                                    @if (count($extensionGroup) > 0)
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach ($extensionGroup as $index => $extension)
                                                <input class="btn-check" type="radio"
                                                    id="extension_{{ str_replace(' ', '-', $extension) }}"
                                                    name="variant_key"
                                                    value="{{ $extensionKey . '-' . preg_replace('/\s+/', '-', $extension) }}"
                                                    {{ $extensionIndex == 0 ? 'checked' : '' }} autocomplete="off">
                                                <label
                                                    class="btn btn-sm check-label border-0 mb-0 w-auto pos-check-label"
                                                    for="extension_{{ str_replace(' ', '-', $extension) }}">
                                                    {{ $extension }}
                                                </label>
                                                @php($extensionIndex++)
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 position-relative price-section mt-3">
                        <div class="alert alert--message flex-row alert-dismissible fade show pos-alert-message pos-bg-warning fs-12 px-12 py-10 text-dark rounded d-flex gap-2 align-items-center justify-content-between d-none"
                            role="alert">
                            <div class="d-flex gap-2 align-items-center">
                                <img class="mb-1"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/warning-icon.png') }}"
                                    alt="{{ translate('warning') }}">
                                <div class="w-0">
                                    <h6>{{ translate('warning') }}</h6>
                                    <div class="product-stock-message"></div>
                                </div>
                            </div>
                            <a href="javascript:" class="align-items-center close-alert-message">
                                <i class="fi fi-sr-cross-small"></i>
                            </a>
                        </div>
                        <div class="default-quantity-system d-none">
                            <div class="d-flex gap-2 align-items-center">
                                <strong class="text-dark">{{ translate('qty') }}:</strong>
                                <div class="product-quantity d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="product-quantity-group input group">
                                            <button type="button"
                                                class="btn-number bg-transparent border-0 shadow-none"
                                                data-type="minus" data-field="quantity" disabled="disabled">
                                                <i class="fi fi-sr-minus fs-10"></i>
                                            </button>
                                            <input type="text" name="quantity"
                                                class="form-control input-number text-center cart-qty-field border-0 shadow-none"
                                                placeholder="1" value="1" min="1" max="100">
                                            <button type="button"
                                                class="btn-number bg-transparent cart-qty-field-plus border-0 shadow-none"
                                                data-type="plus" data-field="quantity">
                                                <i class="fi fi-sr-plus fs-10"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="in-cart-quantity-system d--none">
                            <div class="d-flex gap-2 align-items-center">
                                <strong class="text-dark">{{ translate('qty') }}:</strong>
                                <div class="product-quantity d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="product-quantity-group input group">
                                            <button type="button"
                                                class="btn-number bg-transparent in-cart-quantity-minus action-get-variant-for-already-in-cart border-0 shadow-none"
                                                data-action="minus">
                                                <i class="fi fi-sr-minus fs-10"></i>
                                            </button>
                                            <input type="text" name="quantity_in_cart"
                                                class="form-control text-center in-cart-quantity-field border-0 shadow-none"
                                                placeholder="1" value="1"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value < 1) this.value = 1;"
                                                maxlength="4">

                                            <button type="button"
                                                class="btn-number bg-transparent in-cart-quantity-plus action-get-variant-for-already-in-cart border-0 shadow-none"
                                                data-action="plus">
                                                <i class="fi fi-sr-plus fs-10"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-1 title-color">
                            <div class="product-description-label text-dark fw-bold">{{ translate('total_Price') }}:
                            </div>
                            <div class="product-price text-primary">
                                <strong class="product-details-chosen-price-amount"></strong>
                                <span class="text-muted fs-10 tax-container">
                                    ( {{ ($product->tax_model == 'include' ? '' : '+') . ' ' . translate('tax') }}
                                    <span class="set-product-tax"></span>)</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <button class="btn btn-primary btn-block quick-view-modal-add-cart-button action-add-to-cart"
                            type="button">
                            {{ translate('add_to_cart') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
