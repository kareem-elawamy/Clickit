@extends('layouts.admin.app')

@section('title', translate('product_Preview'))

@section('content')
    <div class="content container-fluid text-start">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-10 mb-3">
            <div class="">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img src="{{ dynamicAsset('public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                    {{ translate('product_details') }}
                </h2>
            </div>
        </div>
        <div class="card card-top-bg-element">
            <div class="card-body">
                <div>
                    <div
                        class="media flex-nowrap flex-column flex-md-row gap-3 flex-grow-1 align-items-center align-items-md-start">
                        <div class="d-flex flex-column align-items-center min-w-165">
                            <div class="position-relative">
                                @if ($product?->clearanceSale)
                                    <div
                                        class="position-absolute z-1 badge badge-warning text-bg-warning user-select-none mb-2">
                                        {{ translate('Clearance_Sale') }}
                                    </div>
                                @endif
                                <a class="aspect-1 float-start overflow-hidden"
                                    href="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}"
                                    data-lightbox="product-gallery-{{ $product['id'] }}">
                                    <img class="avatar avatar-170 rounded object-fit-cover"
                                        src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}"
                                        alt="">
                                </a>
                            </div>
                            <div class="d-flex gap-1 flex-wrap justify-content-center">
                                @if ($productActive && $isActive)
                                    <a href="{{ route('product', $product['slug']) }}"
                                        class="btn btn-outline-primary me-1 mt-2" target="_blank">
                                        <i class="fi fi-rr-globe"></i>
                                        {{ translate('view_live') }}
                                    </a>
                                @endif

                                @if ($product->digital_file_ready_full_url['path'])
                                    <span data-file-path="{{ $product->digital_file_ready_full_url['path'] }}"
                                        class="btn btn-outline-primary me-1 mt-2 text-nowrap d-flex align-items-center justify-content-center gap-1 getDownloadFileUsingFileUrl"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="{{ translate('Download') }}">
                                        <i class="fi fi-rr-download"></i>
                                        <span class="d-block d-md-none">
                                            {{ translate('download') }}
                                        </span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="d-block flex-grow-1 w-max-md-100">
                            @php($languages = getWebConfig(name: 'pnc_language'))
                            @php($defaultLanguage = 'en')
                            @php($defaultLanguage = $languages[0])
                            <div class="row align-items-baseline g-2">
                                <div class="col-md-8">
                                    <div class="position-relative nav--tab-wrapper mb-2">
                                        <ul class="nav nav-pills nav--tab lang_tab" id="pills-tab" role="tablist">
                                            @foreach ($languages as $language)
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link {{ $language == $defaultLanguage ? 'active' : '' }}"
                                                        href="javascript:" data-bs-toggle="pill" role="tab"
                                                        id="{{ $language }}-link">
                                                        {{ getLanguageName($language) . '(' . strtoupper($language) . ')' }}
                                                    </a>
                                                </li>
                                            @endforeach
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
                                <div class="col-md-4">
                                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-end">
                                        @if ($product['added_by'] == 'seller' && ($product['request_status'] == 0 || $product['request_status'] == 1))
                                            <div class="d-flex justify-content-sm-end flex-wrap gap-2 pb-4">
                                                <div>
                                                    <button class="btn btn-danger p-2 px-3" data-bs-toggle="modal"
                                                        data-bs-target="#publishNoteModal">
                                                        {{ translate('reject') }}
                                                    </button>
                                                </div>
                                                <div>
                                                    @if ($product['request_status'] == 0)
                                                        <button class="btn btn-success p-2 px-3 update-status"
                                                            data-id="{{ $product['id'] }}"
                                                            data-redirect-route="{{ route('admin.products.list', ['vendor', 'status' => $product['request_status']]) }}"
                                                            data-message ="{{ translate('want_to_approve_this_product_request') . '?' }}"
                                                            data-status="1">
                                                            {{ translate('approve') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if ($product['added_by'] == 'seller' && $product['request_status'] == 2)
                                            <div class="d-flex justify-content-sm-end flex-wrap gap-2 pb-4">
                                                <div>
                                                    <span>{{ translate('status') . ' : ' }}</span>
                                                    <span
                                                        class="badge text-bg-danger badge-danger">{{ translate('rejected') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div
                                class="d-flex flex-wrap align-items-center flex-sm-nowrap justify-content-between gap-3 min-h-50">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @if ($product->product_type == 'physical' && !empty($product->color_image) && count($product->color_images_full_url) > 0)
                                        @foreach ($product->color_images_full_url as $colorImageKey => $photo)
                                            <div class="{{ $colorImageKey > 4 ? 'd-none' : '' }}">
                                                <a class="aspect-1 w-50px float-start overflow-hidden d-block border rounded-10 position-relative"
                                                    href="{{ getStorageImages(path: $photo['image_name'], type: 'backend-product') }}"
                                                    data-lightbox="product-gallery-{{ $product['id'] }}">
                                                    <img width="50" class="img-fit max-w-50" alt=""
                                                        src="{{ getStorageImages(path: $photo['image_name'], type: 'backend-product') }}">
                                                    @if ($colorImageKey > 3)
                                                        <div class="extra-images">
                                                            <span class="extra-image-count">
                                                                +{{ count($product->color_images_full_url) - $colorImageKey }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach ($product->images_full_url as $imageKey => $photo)
                                            <div class="{{ $imageKey > 4 ? 'd-none' : '' }}">
                                                <a class="aspect-1 w-50px float-start overflow-hidden d-block border rounded-10 position-relative {{ $imageKey > 4 ? 'd-none' : '' }}"
                                                    href="{{ getStorageImages(path: $photo, type: 'backend-product') }}"
                                                    data-lightbox="product-gallery-{{ $product['id'] }}">
                                                    <img width="50" class="img-fit max-w-50" alt=""
                                                        src="{{ getStorageImages(path: $photo, type: 'backend-product') }}">
                                                    @if ($imageKey > 3)
                                                        <div class="extra-images">
                                                            <span class="extra-image-count">
                                                                +{{ count($product->images_full_url) - $imageKey }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                                <div
                                    class="d-flex gap-3 flex-nowrap lh-1 badge text-bg-info badge-info fw-semibold justify-content-sm-end h-30 align-items-center">
                                    <span class="text-dark">
                                        {{ count($product->orderDetails) }} {{ translate('orders') }}
                                    </span>
                                    <span class="border-start border-secondary-subtle py-2"></span>
                                    <div
                                        class="review-hover position-relative cursor-pointer d-flex gap-2 align-items-center">
                                        <i class="fi fi-sr-star"></i>
                                        <span>
                                            {{ count($product->rating) > 0 ? number_format($product->rating[0]->average, 2, '.', ' ') : 0 }}
                                        </span>
                                        <div class="review-details-popup">
                                            <h6 class="mb-2">{{ translate('rating') }}</h6>
                                            <div class="">
                                                <ul class="list-unstyled list-unstyled-py-2 d-flex flex-column gap-2 mb-0">
                                                    @php($total = $product->reviews->count())

                                                    <li class="d-flex align-items-center fs-12">
                                                        @php($five = getRatingCount($product['id'], 5))
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'ml-3' : 'me-3' }}">
                                                            {{ translate('5') }} {{ translate('star') }}
                                                        </span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $total == 0 ? 0 : ($five / $total) * 100 }}%;"
                                                                aria-valuenow="{{ $total == 0 ? 0 : ($five / $total) * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'me-3' : 'ml-3' }}">{{ $five }}</span>
                                                    </li>

                                                    <li class="d-flex align-items-center fs-12">
                                                        @php($four = getRatingCount($product['id'], 4))
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'ml-3' : 'me-3' }}">{{ translate('4') }}
                                                            {{ translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $total == 0 ? 0 : ($four / $total) * 100 }}%;"
                                                                aria-valuenow="{{ $total == 0 ? 0 : ($four / $total) * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'me-3' : 'ml-3' }}">{{ $four }}</span>
                                                    </li>

                                                    <li class="d-flex align-items-center fs-12">
                                                        @php($three = getRatingCount($product['id'], 3))
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'ml-3' : 'me-3' }}">{{ translate('3') }}
                                                            {{ translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $total == 0 ? 0 : ($three / $total) * 100 }}%;"
                                                                aria-valuenow="{{ $total == 0 ? 0 : ($three / $total) * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'me-3' : 'ml-3' }}">{{ $three }}</span>
                                                    </li>

                                                    <li class="d-flex align-items-center fs-12">
                                                        @php($two = getRatingCount($product['id'], 2))
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'ml-3' : 'me-3' }}">{{ translate('2') }}
                                                            {{ translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $total == 0 ? 0 : ($two / $total) * 100 }}%;"
                                                                aria-valuenow="{{ $total == 0 ? 0 : ($two / $total) * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'me-3' : 'ml-3' }}">{{ $two }}</span>
                                                    </li>

                                                    <li class="d-flex align-items-center fs-12">
                                                        @php($one = getRatingCount($product['id'], 1))
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'ml-3' : 'me-3' }}">{{ translate('1') }}
                                                            {{ translate('star') }}</span>
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $total == 0 ? 0 : ($one / $total) * 100 }}%;"
                                                                aria-valuenow="{{ $total == 0 ? 0 : ($one / $total) * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span
                                                            class="{{ Session::get('direction') === 'rtl' ? 'me-3' : 'ml-3' }}">{{ $one }}</span>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="border-start border-secondary-subtle py-2"></span>
                                    <span class="text-dark">
                                        {{ $product->reviews->whereNotNull('comment')->count() }}
                                        {{ translate('reviews') }}
                                    </span>
                                </div>
                            </div>
                            <div class="tab-content mt-3" id="pills-tabContent">
                                @foreach ($languages as $language)
                                    <?php
                                    if (count($product['translations'])) {
                                        $translate = [];
                                        foreach ($product['translations'] as $translation) {
                                            if ($translation->locale == $language && $translation->key == 'name') {
                                                $translate[$language]['name'] = $translation->value;
                                            }
                                            if ($translation->locale == $language && $translation->key == 'description') {
                                                $translate[$language]['description'] = $translation->value;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="tab-pane fade {{ $language == $defaultLanguage ? 'show active' : '' }}"
                                        id="{{ $language }}-form" aria-labelledby="{{ $language }}-link"
                                        role="tabpanel">
                                        <div class="d-flex gap-2 align-items-center mb-2">
                                            <h2 class="h1 mb-0 text-dark">
                                                {{ $translate[$language]['name'] ?? $product['name'] }}</h2>
                                            <a class="btn btn-outline-primary icon-btn" title="{{ translate('edit') }}"
                                                href="{{ route('admin.products.update', [$product['id']]) }}">
                                                <i class="fi fi-sr-pencil"></i>
                                            </a>
                                        </div>
                                        <div class="">
                                            <label class="fw-bold mb-2">{{ translate('description') . ' : ' }}</label>
                                            <div class="rich-editor-html-content">
                                                {!! $translate[$language]['description'] ?? $product['details'] !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex gap-3 flex-wrap">
                    <div class="border p-3 w-100-mobile w-170">
                        <div class="d-flex flex-column mb-1">
                            <h6 class="fw-normal text-capitalize">{{ translate('total_sold') }} :</h6>
                            <h3 class="text-primary fs-18">{{ $product['qtySum'] }}</h3>
                        </div>
                        <div class="d-flex flex-column">
                            <h6 class="fw-normal text-capitalize">{{ translate('total_sold_amount') }} :</h6>
                            <h3 class="text-primary fs-18">
                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $product['priceSum'] - $product['discountSum'])) }}
                            </h3>
                        </div>
                    </div>

                    <div class="row gy-3 flex-grow-1">
                        <div class="col-sm-6 col-xl-4">
                            <h4 class="mb-3 text-capitalize">{{ translate('general_information') }}</h4>

                            <div class="pair-list">
                                @if ($product?->product_type == 'physical')
                                    @if (isset($product->brand) && !empty($product->brand->default_name))
                                        <div>
                                            <span class="key">{{ translate('brand') }}</span>
                                            <span>:</span>
                                            <span class="value">{{ $product->brand->default_name }}</span>
                                        </div>
                                    @endif
                                @endif

                                @if (isset($product->category) && !empty($product->category->default_name))
                                    <div>
                                        <span class="key">{{ translate('category') }}</span>
                                        <span>:</span>
                                        <span class="value">
                                            {{ $product->category->default_name }}
                                        </span>
                                    </div>
                                @endif

                                <div>
                                    <span class="key text-capitalize">{{ translate('product_type') }}</span>
                                    <span>:</span>
                                    <span class="value">{{ translate($product->product_type) }}</span>
                                </div>
                                @if ($product->product_type == 'physical')
                                    <div>
                                        <span class="key text-capitalize">{{ translate('product_unit') }}</span>
                                        <span>:</span>
                                        <span class="value">{{ $product['unit'] }}</span>
                                    </div>
                                    <div>
                                        <span class="key">{{ translate('current_Stock') }}</span>
                                        <span>:</span>
                                        <span class="value">{{ $product->current_stock }}</span>
                                    </div>
                                @endif
                                <div>
                                    <span class="key">{{ translate('product_SKU') }}</span>
                                    <span>:</span>
                                    <span class="value">{{ $product->code }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <h4 class="mb-3 text-capitalize">{{ translate('price_information') }}</h4>

                            <div class="pair-list">
                                <div>
                                    <span class="key text-capitalize">{{ translate('unit_price') }}</span>
                                    <span>:</span>
                                    <span class="value">
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $product->unit_price), currencyCode: getCurrencyCode()) }}
                                    </span>
                                </div>

                                <div>
                                    <span class="key">{{ translate('tax') }}</span>
                                    <span>:</span>
                                    @if ($product->tax_type == 'percent')
                                        <span class="value">
                                            {{ $product->tax }}% ({{ $product->tax_model }})
                                        </span>
                                    @else
                                        <span class="value">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $product->tax)) }}
                                            ({{ $product->tax_model }})
                                        </span>
                                    @endif
                                </div>
                                @if ($product->product_type == 'physical')
                                    <div>
                                        <span class="key text-capitalize">{{ translate('shipping_cost') }}</span>
                                        <span>:</span>
                                        <span class="value">
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $product->shipping_cost)) }}
                                            @if ($product->multiply_qty == 1)
                                                ({{ translate('multiply_with_quantity') }})
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                @if (getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                    <div>
                                        <span class="key text-capitalize">{{ translate('discount') }}</span>
                                        <span>:</span>
                                        @if ($product?->clearanceSale?->discount_type == 'percentage' ? '-' : '')
                                        @endif
                                        {{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if (count($product->tags) > 0)
                            <div class="col-sm-6 col-xl-4">
                                <h4 class="mb-3">{{ translate('tags') }}</h4>
                                <div class="pair-list">
                                    <div>
                                        <span class="value">
                                            @foreach ($product->tags as $key => $tag)
                                                {{ $tag['tag'] }}
                                                @if ($key === count($product->tags) - 1)
                                                    @break
                                                @endif
                                                ,
                                            @endforeach
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-2 mt-3">
            @if (!empty($product['variation']) && count(json_decode($product['variation'])) > 0)
                <div class="col-md-12">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th class="text-center">{{ translate('SKU') }}</th>
                                            <th class="text-center text-capitalize">
                                                {{ translate('variation_wise_price') }}</th>
                                            <th class="text-center">{{ translate('stock') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (json_decode($product['variation']) as $key => $value)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="py-1">{{ $value->sku }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="py-1">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $value->price), currencyCode: getCurrencyCode()) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="py-1">{{ $value->qty }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($product->digitalVariation) && count($product->digitalVariation) > 0)
                <div class="col-md-12">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th class="text-center">{{ translate('SL') }}</th>
                                            <th class="text-center">{{ translate('Variation_Name') }}</th>
                                            <th class="text-center">{{ translate('SKU') }}</th>
                                            <th class="text-center">{{ translate('price') }}</th>
                                            @if ($product->digital_product_type == 'ready_product')
                                                <th class="text-center">{{ translate('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->digitalVariation as $key => $variation)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $key + 1 }}
                                                </td>
                                                <td class="text-center text-capitalize">
                                                    <span class="py-1">{{ $variation->variant_key ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="py-1">{{ $variation->sku }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="py-1">
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $variation->price), currencyCode: getCurrencyCode()) }}
                                                    </span>
                                                </td>

                                                @if ($product->digital_product_type == 'ready_product')
                                                    <td class="text-center">
                                                        <span class="btn p-0 getDownloadFileUsingFileUrl"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-title="{{ !is_null($variation->file_full_url['path']) ? translate('download') : translate('File_not_found') }}"
                                                            data-file-path="{{ $variation->file_full_url['path'] }}">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/download-green.svg') }}"
                                                                alt="">
                                                        </span>
                                                    </td>
                                                @endif

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h3 class="text-dark mb-0">{{ translate('product_SEO_&_meta_data') }}</h3>
                    </div>
                    <div class="card-body">
                        <div>
                            <h4 class="mb-3 text-capitalize">
                                {{ $product?->seoInfo?->title ?? ($product->meta_title ?? translate('meta_title_not_found') . ' ' . '!') }}
                            </h4>
                        </div>
                        <p class="text-capitalize">
                            {{ $product?->seoInfo?->description ?? ($product->meta_description ?? translate('meta_description_not_found') . ' ' . '!') }}
                        </p>
                        @if ($product?->seoInfo?->image_full_url['path'] || $product->meta_image_full_url['path'])
                            <div class="d-flex flex-wrap gap-2">
                                <a data-bs-toggle="modal" data-bs-target="#imgViewModal-seo-{{ $product->id }}"
                                    data-type="image" href="javascript:" class="aspect-1 float-start overflow-hidden"
                                    data-index="1">
                                    <img class="max-w-100px rounded"
                                        src="{{ getStorageImages(path: $product?->seoInfo?->image_full_url['path'] ? $product?->seoInfo?->image_full_url : $product->meta_image_full_url, type: 'backend-product') }}"
                                        alt="{{ translate('meta_image') }}">
                                </a>
                                <div class="modal fade imgViewModal" id="imgViewModal-seo-{{ $product->id }}"
                                    tabindex="-1" aria-labelledby="imgViewModal-seo-{{ $product->id }}Label"
                                    role="dialog" aria-modal="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content bg-transparent border-0">
                                            <div class="modal-body pt-0">
                                                <div class="imgView-slider owl-theme owl-carousel" dir="ltr">
                                                    <div class="imgView-item">
                                                        <div class="d-flex justify-content-between align-items-end">
                                                            <a href="{{ getStorageImages(path: $product?->seoInfo?->image_full_url['path'] ? $product?->seoInfo?->image_full_url : $product->meta_image_full_url, type: 'backend-product') }}"
                                                                class="d-flex align-items-center gap-2 mb-2">
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-close p-1 border-0 fs-10"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                            </button>
                                                        </div>
                                                        <div class="image-wrapper">
                                                            <div class="position-relative">
                                                                <div class="image-wrapper">
                                                                    <img class="image" alt=""
                                                                        src="{{ getStorageImages(path: $product?->seoInfo?->image_full_url['path'] ? $product?->seoInfo?->image_full_url : $product->meta_image_full_url, type: 'backend-product') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h3 class="text-dark mb-0">{{ translate('product_video') }}</h3>
                    </div>
                    <div class="card-body">
                        <div>
                            <h4 class="mb-3 text-capitalize">
                                {{ $product['video_provider'] . ' ' . translate('video_link') }}
                            </h4>
                        </div>
                        @if ($product['video_url'])
                            <a href="{{ str_contains($product->video_url, 'https://') || str_contains($product->video_url, 'http://') ? $product['video_url'] : 'javascript:' }}"
                                target="_blank"
                                class="text-primary {{ str_contains($product->video_url, 'https://') || str_contains($product->video_url, 'http://') ? '' : 'cursor-default' }}">
                                {{ $product['video_url'] }}
                            </a>
                        @else
                            <span>{{ translate('no_data_to_show') . ' ' . '!' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @if ($product->denied_note && $product['request_status'] == 2)
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header bg-primary bg-opacity-10">
                            <h3 class="text-dark mb-0">{{ translate('reject_reason') }}</h3>
                        </div>
                        <div class="card-body">
                            <div>
                                {{ $product->denied_note }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="card mt-3">
            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-borderless table-thead-bordered align-middle">
                    <thead class="text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('Review_ID') }}</th>
                            <th>{{ translate('reviewer') }}</th>
                            <th>{{ translate('rating') }}</th>
                            <th>{{ translate('review') }}</th>
                            <th>{{ translate('Reply') }}</th>
                            <th class="text-center">{{ translate('date') }}</th>
                            <th class="text-center">{{ translate('Status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviews as $key => $review)
                            @if (isset($review->customer))
                                <tr>
                                    <td>{{ $reviews->firstItem() + $key }}</td>
                                    <td class="text-center">
                                        {{ $review->id }}
                                    </td>
                                    <td>
                                        <a class="d-flex gap-2 align-items-center"
                                            href="{{ route('admin.customer.view', [$review['customer_id']]) }}">
                                            <div class="avatar rounded">
                                                <img class="avatar-img aspect-1"
                                                    src="{{ getStorageImages(path: $review->customer->image_full_url, type: 'backend-profile') }}"
                                                    alt="">
                                            </div>
                                            <div>
                                                <span class="d-block h5 text-hover-primary mb-0">
                                                    {{ $review->customer['f_name'] . ' ' . $review->customer['l_name'] }}
                                                    <i class="fi fi-sr-shield-trust text-primary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" data-bs-title="Verified Customer"></i>
                                                </span>
                                                <span class="d-block text-body">
                                                    {{ $review->customer->email ?? '' }}
                                                </span>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2 text-primary">
                                            <i class="fi fi-sr-star"></i>
                                            <span>{{ $review->rating }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap max-w-400 min-w-200">
                                            <p class="line-2 max-w-250 word-break">
                                                {{ $review['comment'] }}
                                            </p>
                                            @if (count($review->attachment_full_url) > 0)
                                                @foreach ($review->attachment_full_url as $img)
                                                    <a class="aspect-1 float-start overflow-hidden"
                                                        href="{{ getStorageImages(path: $img, type: 'backend-product') }}"
                                                        data-lightbox="review-gallery{{ $review['id'] }}">
                                                        <img class="p-2" width="60" height="60"
                                                            src="{{ getStorageImages(path: $img, type: 'backend-product') }}"
                                                            alt="{{ translate('review_image') }}">
                                                    </a>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="line-2 max-w-250 word-break">
                                            {{ $review?->reply?->reply_text ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ date('d M Y H:i:s', strtotime($review['created_at'])) }}
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.reviews.status') }}" method="POST"
                                            id="reviews-status{{ $review['id'] }}-form">
                                            @csrf
                                            <input name="id" value="{{ $review['id'] }}" hidden>
                                            <label class="switcher mx-auto" for="reviews-status{{ $review['id'] }}">
                                                <input class="switcher_input custom-modal-plugin" type="checkbox"
                                                    value="1" name="status"
                                                    id="reviews-status{{ $review['id'] }}"
                                                    {{ $review['status'] == 1 ? 'checked' : '' }}
                                                    data-modal-type="input-change-form"
                                                    data-modal-form="#reviews-status{{ $review['id'] }}-form"
                                                    data-on-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/customer-reviews-on.png') }}"
                                                    data-off-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/customer-reviews-off.png') }}"
                                                    data-on-title="{{ translate('Want_to_Turn_ON_Customer_Reviews') }}"
                                                    data-off-title="{{ translate('Want_to_Turn_OFF_Customer_Reviews') }}"
                                                    data-on-message="<p>{{ translate('if_enabled_anyone_can_see_this_review_on_the_user_website_and_customer_app') }}</p>"
                                                    data-off-message="<p>{{ translate('if_disabled_this_review_will_be_hidden_from_the_user_website_and_customer_app') }}</p>"
                                                    data-on-button-text="{{ translate('turn_on') }}"
                                                    data-off-button-text="{{ translate('turn_off') }}">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <div data-bs-toggle="modal"
                                                data-bs-target="#review-view-for-{{ $review['id'] }}">
                                                <a class="btn btn-outline-info icon-btn"
                                                    data-bs-title="{{ translate('View') }}" data-bs-toggle="tooltip">
                                                    <i class="fi fi-rr-eye"></i>
                                                </a>
                                            </div>

                                            @if (isset($review->product) && $review?->product?->added_by == 'admin')
                                                <div data-bs-toggle="modal"
                                                    data-bs-target="#review-update-for-{{ $review['id'] }}">
                                                    @if ($review?->reply)
                                                        <a class="btn btn-outline-primary icon-btn"
                                                            data-bs-title="{{ translate('Update_Review') }}"
                                                            data-bs-toggle="tooltip">
                                                            <i class="fi fi-sr-pencil"></i>
                                                        </a>
                                                    @else
                                                        <div class="btn btn-outline-primary icon-btn"
                                                            data-bs-title="{{ translate('Review_Reply') }}"
                                                            data-bs-toggle="tooltip">
                                                            <i class="fi fi-sr-reply-all"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($review?->product?->added_by == 'seller')
                                                <div>
                                                    <a class="btn btn-outline-primary icon-btn"
                                                        data-bs-title="{{ translate('Admin_can_not_reply_to_vendor_product_review') }}"
                                                        data-bs-toggle="tooltip">
                                                        @if ($review?->reply)
                                                            <i class="fi fi-sr-pencil"></i>
                                                        @else
                                                            <i class="fi fi-sr-reply-all"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                            @else
                                                <div>
                                                    <a class="btn btn-outline-primary icon-btn"
                                                        data-bs-title="{{ translate('product_not_found') }}"
                                                        data-bs-toggle="tooltip">
                                                        @if ($review?->reply)
                                                            <i class="fi fi-sr-pencil"></i>
                                                        @else
                                                            <i class="fi fi-sr-reply-all"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach ($reviews as $key => $review)
                @if (isset($review->customer))
                    <div class="modal fade" id="review-update-for-{{ $review['id'] }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close text-BFBFBF" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <i class="fi fi-sr-cross-circle"></i>
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('admin.reviews.add-review-reply') }}">
                                    @csrf
                                    <div class="modal-body pt-0">
                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                            <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}"
                                                width="100" class="rounded aspect-1 border" alt="">
                                            <div class="w-0 flex-grow-1 fw-semibold">
                                                @if ($review['order_id'])
                                                    <div class="mb-2">
                                                        {{ translate('Order_ID') }} # {{ $review['order_id'] }}
                                                    </div>
                                                @endif
                                                <h4>{{ $translate[$language]['name'] ?? $product['name'] }}</h4>
                                            </div>
                                        </div>
                                        <label class="input-label text--title fw-bold">
                                            {{ translate('Review') }}
                                        </label>
                                        <div class="__bg-F3F5F9 p-3 rounded border mb-2">
                                            {{ $review['comment'] }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if (count($review->attachment_full_url) > 0)
                                                @foreach ($review->attachment_full_url as $img)
                                                    <a class="aspect-1 float-start overflow-hidden"
                                                        href="{{ getStorageImages(path: $img, type: 'backend-product') }}"
                                                        data-lightbox="review-gallery-modal{{ $review['id'] }}">
                                                        <img width="45" class="rounded aspect-1 border"
                                                            src="{{ getStorageImages(path: $img, type: 'backend-product') }}"
                                                            alt="{{ translate('review_image') }}">
                                                    </a>
                                                @endforeach
                                            @endif
                                        </div>
                                        <label class="input-label text--title fw-bold pt-4">
                                            {{ translate('Reply') }}
                                        </label>
                                        <input type="hidden" name="review_id" value="{{ $review['id'] }}">
                                        <textarea class="form-control text-area-max-min" rows="3" name="reply_text"
                                            placeholder="{{ translate('Write_the_reply_of_the_product_review') }}...">{{ $review?->reply?->reply_text ?? '' }}</textarea>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn btn--primary">
                                                @if ($review?->reply?->reply_text)
                                                    {{ translate('Update') }}
                                                @else
                                                    {{ translate('submit') }}
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="modal fade" id="review-view-for-{{ $review['id'] }}" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close text-BFBFBF" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i class="fi fi-sr-cross-circle"></i>
                                </button>
                            </div>
                            <div class="modal-body pt-0">
                                <div class="d-flex flex-wrap align-items-center gap-3 mb-3 text-center border-bottom">
                                    <div class="w-0 flex-grow-1 fw-semibold">
                                        <div class="mb-2">
                                            {{ translate('Review_ID') }} # {{ $review['id'] }}
                                        </div>

                                        @if ($review['order_id'])
                                            <div class="mb-2">
                                                {{ translate('Order_ID') }} # {{ $review['order_id'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <h2 class="text-center">
                                    <span class="text-primary">{{ $review['rating'] . '.0' }}</span><span
                                        class="fz-16 text-muted">{{ '/5' }}</span>
                                </h2>
                                <div
                                    class="d-flex align-items-center gap-1 text-primary justify-content-center fz-14 mb-4">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review['rating'])
                                            <i class="fi fi-sr-star"></i>
                                        @else
                                            <i class="fi fi-rr-star"></i>
                                        @endif
                                    @endfor
                                </div>

                                <label class="input-label text--title fw-bold">
                                    {{ translate('Review') }}
                                </label>
                                <div class="__bg-F3F5F9 p-3 rounded border mb-2">
                                    {{ $review['comment'] }}
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @if (count($review->attachment_full_url) > 0)
                                        @foreach ($review->attachment_full_url as $img)
                                            <a class="aspect-1 float-start overflow-hidden"
                                                href="{{ getStorageImages(path: $img, type: 'backend-product') }}"
                                                data-lightbox="review-gallery-modal{{ $review['id'] }}">
                                                <img width="45" class="rounded aspect-1 border"
                                                    src="{{ getStorageImages(path: $img, type: 'backend-product') }}"
                                                    alt="{{ translate('review_image') }}">
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                                @if ($review?->reply?->reply_text)
                                    <label class="input-label text--title fw-bold pt-4">
                                        {{ translate('Reply') }}
                                    </label>
                                    <div class="__bg-F3F5F9 p-3 rounded border mb-2">
                                        {{ $review?->reply?->reply_text ?? '' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="table-responsive mt-4">
                <div class="px-4 d-flex justify-content-lg-end">
                    {!! $reviews->links() !!}
                </div>
            </div>

            @if (count($reviews) == 0)
                @include(
                    'layouts.admin.partials._empty-state',
                    ['text' => 'no_review_found'],
                    ['image' => 'default']
                )
            @endif
        </div>
    </div>

    <div class="modal fade" id="publishNoteModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 d-flex justify-content-between align-items-center gap-3">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('rejected_note') }}</h5>
                    <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none"
                        data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form class="form-group" action="{{ route('admin.products.deny', ['id' => $product['id']]) }}"
                    method="post" id="product-status-denied">
                    @csrf
                    <div class="modal-body">
                        <textarea class="form-control text-area-max-min" name="denied_note" rows="3" data-maxlength="100"></textarea>
                        <span id="denied-note-word-count" class="text-body-light">{{ translate('0/100') }}</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ translate('close') }}
                        </button>
                        <button type="button" class="btn btn-primary form-submit"
                            data-message="{{ translate('want_to_reject_this_product_request') }}"
                            data-redirect-route="{{ route('admin.products.list', ['vendor', 'status' => $product['request_status']]) }}"
                            data-form-id="product-status-denied">{{ translate('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <span id="get-update-status-route" data-action="{{ route('admin.products.approve-status') }}"></span>
@endsection
@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-view.js') }}"></script>
@endpush
