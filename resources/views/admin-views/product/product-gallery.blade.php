@extends('layouts.admin.app')
@section('title', translate('product_gallery'))

@section('content')
    <div class="content container-fluid">
        <div>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <h2 class="h1 mb-0">
                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/all-orders.png') }}" class="mb-1 mr-1"
                        alt="">
                    {{ translate('product_gallery') }}
                </h2>
                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $products->total() }}</span>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row gx-2 gy-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label" for="store">{{ translate('store') }}</label>
                                <select name="vendor_id" class="custom-select product-gallery-filter">
                                    <option value="all">{{ translate('all_shop') }}</option>
                                    <option value="in_house" {{ 'in_house' == request('vendor_id') ? 'selected' : '' }}>
                                        {{ translate('in-house') }}</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor['id'] }}"
                                            {{ $vendor['id'] == request('vendor_id') ? 'selected' : '' }}>
                                            {{ $vendor->shop['name'] ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label" for="store">{{ translate('brand') }}</label>
                                <select name="brand_id" class="custom-select product-gallery-filter">
                                    <option value="all">{{ translate('all_brand') }}</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand['id'] }}"
                                            {{ $brand['id'] == request('brand_id') ? 'selected' : '' }}>
                                            {{ $brand['defaultName'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="form-label" for="store">{{ translate('category') }}</label>
                                <select name="category_id" class="custom-select product-gallery-filter">
                                    <option value="all">{{ translate('all_category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ $category['id'] == request('category_id') ? 'selected' : '' }}>
                                            {{ $category['defaultName'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 d-flex align-items-end">
                            <div class="flex-grow-1">
                                <form action="{{ route('admin.products.product-gallery') }}">
                                    <div class="input-group">
                                        <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                                        <input type="hidden" name="vendor_id" value="{{ request('vendor_id') }}">
                                        <input type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_product_name') }}"
                                            aria-label="Search orders" value="{{ request('searchValue') }}">
                                        <div class="input-group-append search-submit">
                                            <button type="submit">
                                                <i class="fi fi-rr-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($products as $product)
                        <div class="mb-3 border rounded bg-white p-3">
                            <div
                                class="d-flex gap-3 flex-wrap flex-md-nowrap justify-content-center justify-content-md-start">
                                <div class="media flex-nowrap flex-column flex-sm-row gap-3">
                                    <div class="d-flex flex-column align-items-center min-w-165">
                                        <a class="aspect-1 float-start overflow-hidden"
                                            href="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}"
                                            data-lightbox="product-gallery-{{ $product['id'] }}">
                                            <img class="avatar avatar-170 rounded object-fit-cover"
                                                src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}"
                                                alt="">
                                        </a>
                                    </div>
                                </div>
                                <div class="row gy-2 flex-grow-1">
                                    <div class="col-12">
                                        <div class="d-md-flex justify-content-md-between">
                                            <h3 class="text-capitalize">{{ $product['name'] }}</h3>
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('admin.products.update', ['id' => $product['id'], 'product-gallery' => 1]) }}">
                                                {{ translate('use_this_product_info') }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <h3 class="mb-3 text-capitalize">{{ translate('general_information') }}</h3>
                                        <div class="pair-list">
                                            @if (isset($product->brand) && !empty($product->brand->default_name))
                                                <div>
                                                    <span class="key">{{ translate('brand') }}</span>
                                                    <span>:</span>
                                                    <span class="value">{{ $product->brand->default_name }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="key">{{ translate('category') }}</span>
                                                <span>:</span>
                                                <span class="value">
                                                    {{ isset($product->category) ? $product->category->default_name : translate('category_not_found') }}
                                                </span>
                                            </div>

                                            <div>
                                                <span class="key text-capitalize">{{ translate('product_type') }}</span>
                                                <span>:</span>
                                                <span class="value">{{ translate($product->product_type) }}</span>
                                            </div>
                                            @if ($product->product_type == 'physical')
                                                <div>
                                                    <span
                                                        class="key text-capitalize">{{ translate('product_unit') }}</span>
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

                                    <?php
                                    $fileTypes = is_array($product->digital_product_file_types)
                                    ? $product->digital_product_file_types
                                    : json_decode($product->digital_product_file_types, true) ?? [];

                                    $extensions = is_array($product->digital_product_extensions)
                                    ? $product->digital_product_extensions
                                    : json_decode($product->digital_product_extensions, true) ?? [];
                                    ?>
                                    @if (($product->product_type == 'physical' && (count(json_decode($product->choice_options)) > 0) ||count(json_decode($product->colors)) > 0))
                                        <div class="col-sm-6 col-xl-4">
                                            <h3 class="mb-3">{{ translate('available_variations') }}</h3>
                                            <div class="pair-list">
                                                @if (json_decode($product->choice_options) != null)
                                                    @foreach (json_decode($product->choice_options) as $key => $value)
                                                        <div>
                                                            @if (array_filter($value->options) != null)
                                                                <span class="key">{{ translate($value->title) }}</span>
                                                                <span>:</span>
                                                                <span class="value">
                                                                    @foreach ($value->options as $index => $option)
                                                                        {{ $option }}
                                                                        @if ($index === array_key_last($value->options))
                                                                            @break
                                                                        @endif
                                                                        ,
                                                                    @endforeach
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if (is_array($product['colorsName']) && count($product['colorsName']) > 0)
                                                    <div>
                                                        <span class="key">{{ translate('color') }}</span>
                                                        <span>:</span>
                                                        <span class="value">
                                                            @foreach ($product['colorsName'] as $key => $color)
                                                                {{ $color }}@if ($key !== array_key_last($product['colorsName']))
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        </span>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @elseif ($product->product_type == 'digital' &&(count($fileTypes) > 0 || count($extensions) > 0))
                                    <div class="col-sm-6 col-xl-4">
                                        <h3 class="mb-3">{{ translate('available_variations') }}</h3>
                                        <div class="pair-list">

                                            @php
                                                $extensions = is_array($product->digital_product_extensions)
                                                    ? $product->digital_product_extensions
                                                    : json_decode($product->digital_product_extensions, true);

                                            @endphp

                                        @if (!empty($extensions))
                                            <div>
                                                <span class="value" style="display: block;">
                                                    @foreach ($extensions as $index => $ext)
                                                        <div>{{ $index }}: {{ implode(', ', $ext) }}</div>
                                                    @endforeach
                                                </span>
                                            </div>
                                        @endif


                                        </div>
                                    </div>
                                    @endif
                                    @if (count($product->tags) > 0)
                                        <div class="col-sm-6 col-xl-4">
                                            <h3 class="mb-3">{{ translate('tags') }}</h3>
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
                            <div class="mt-3">
                                <div class="view--more rich-editor-html-content">
                                    <label class="text-gulf-blue fw-bold">{{ translate('description') . ' : ' }}</label>
                                    <div>
                                        {!! $product['details'] !!}
                                    </div>
                                    <button class="expandable-btn d-none">
                                        <span class="more">{{ translate('View More') }}</span>
                                        <span class="less d-none">{{ translate('View Less') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if (count($products) <= 0)
                        @include(
                            'layouts.admin.partials._empty-state',
                            ['text' => 'no_product_found'],
                            ['image' => 'default']
                        )
                    @endif
                </div>
            </div>
            <div class=" mt-4">
                <div class="px-4 d-flex justify-content-lg-end">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
    <span id="get-product-gallery-route" data-action="{{ route('admin.products.product-gallery') }}"
        data-brand-id="{{ request('brand_id') }}" data-category-id="{{ request('category_id') }}"
        data-vendor-id="{{ request('vendor_id') }}">
    @endsection
