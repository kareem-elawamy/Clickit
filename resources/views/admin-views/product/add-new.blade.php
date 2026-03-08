@extends('layouts.admin.app')

@section('title', translate('product_Add'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('Add_New_Product') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.products.store') }}" method="POST"
              enctype="multipart/form-data" id="product_form">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-9">
                            <div class="position-relative nav--tab-wrapper">
                                <ul class="nav nav-pills nav--tab text-capitalize lang_tab" id="pills-tab"
                                    role="tablist">
                                    @foreach ($languages as $lang)
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                               id="{{ $lang }}-link" data-bs-toggle="pill" href="#{{ $lang }}-form"
                                               role="tab" aria-controls="{{ $lang }}-form" aria-selected="true">
                                                {{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}
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
                        <div class="col-md-3">
                            <div class="d-flex justify-content-end">
                                <a class="btn btn-primary btn-sm p-2 text-capitalize"
                                   href="{{ route('admin.products.product-gallery') }}">
                                    <i class="fi fi-rr-plus-small"></i>
                                    {{ translate('add_info_from_gallery') }}
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="tab-content" id="pills-tabContent">
                                @foreach ($languages as $lang)
                                    <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active' : '' }}"
                                         id="{{ $lang }}-form" role="tabpanel">
                                        <div class="form-group">
                                            <label class="form-label"
                                                   for="{{ $lang }}_name">{{ translate('product_name') }}
                                                ({{ strtoupper($lang) }})
                                                @if($lang == $defaultLanguage)
                                                    <span class="input-required-icon text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text"
                                                   {{ $lang == $defaultLanguage ? 'required' : '' }} name="name[]"
                                                   id="{{ $lang }}_name"
                                                   class="form-control {{ $lang == $defaultLanguage ? 'product-title-default-language' : '' }}"
                                                   placeholder="{{ translate('ex') }}: {{ translate('new_Product') }}">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        <div class="form-group pt-2">
                                            <label class="form-label" for="{{ $lang }}_description">
                                                {{ translate('description') }} ({{ strtoupper($lang) }})
                                                @if($lang == $defaultLanguage)
                                                    <span class="input-required-icon text-danger">*</span>
                                                @endif
                                            </label>

                                            <div id="description-{{$lang }}-editor" class="quill-editor"></div>
                                            <textarea name="description[]" id="description-{{$lang }}"
                                                      style="display:none;"></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="fi fi-sr-user"></i>
                        <h3 class="mb-0">{{ translate('general_setup') }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    {{ translate('category') }}
                                    <span class="input-required-icon">*</span>
                                </label>
                                <select class="custom-select action-get-request-onchange" name="category_id"
                                        data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"
                                        data-element-id="sub-category-select"
                                        data-element-type="select"
                                        data-placeholder="{{ translate('select_category') }}"
                                        required>
                                    <option value="{{ old('category_id') }}" selected
                                            disabled>{{ translate('select_category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ old('name') == $category['id'] ? 'selected' : '' }}>
                                            {{ $category['defaultName'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ translate('sub_Category') }}</label>
                                <select class="custom-select action-get-request-onchange" name="sub_category_id"
                                        id="sub-category-select"
                                        data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"
                                        data-element-id="sub-sub-category-select"
                                        data-element-type="select"
                                        data-placeholder="{{ translate('select_Sub_Category') }}"
                                >
                                    <option value="{{ null }}" selected
                                            disabled>{{ translate('select_Sub_Category') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ translate('sub_Sub_Category') }}</label>
                                <select class="custom-select" name="sub_sub_category_id"
                                        id="sub-sub-category-select"
                                        data-placeholder="{{ translate('select_Sub_Sub_Category') }}"
                                >
                                    <option value="{{ null }}" selected disabled>
                                        {{ translate('select_Sub_Sub_Category') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        @if($brandSetting)
                            <div class="col-md-6 col-lg-4 col-xl-3 show-for-physical-product">
                                <div class="form-group">
                                    <label class="form-label">
                                        {{ translate('brand') }}
                                        <span class="input-required-icon">*</span>
                                    </label>
                                    <select class="custom-select" name="brand_id" required>
                                        <option value="{{ null }}" selected
                                                disabled>{{ translate('select_Brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand['id'] }}">{{ $brand['defaultName'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ translate('product_type') }}
                                    <span class="input-required-icon">*</span>
                                </label>
                                <div class="select-wrapper">
                                    <select name="product_type" id="product_type" class="form-select" required>
                                        <option value="physical" selected>{{ translate('physical') }}</option>
                                        @if($digitalProductSetting)
                                            <option value="digital">{{ translate('digital') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3 show-for-digital-product">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ translate("Author") }}/{{ translate("Creator") }}/{{ translate("Artist") }}
                                </label>
                                <select class="custom-select tags" name="authors[]" multiple="multiple" id="mySelect">
                                    @foreach($digitalProductAuthors as $authors)
                                        <option value="{{ $authors['name'] }}">{{ $authors['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3 show-for-digital-product">
                            <div class="form-group">
                                <label class="form-label">{{ translate("Publishing_House") }}</label>
                                <select class="custom-select tags" name="publishing_house[]" multiple="multiple">

                                    @foreach($publishingHouseList as $publishingHouse)
                                        <option value="{{ $publishingHouse['name'] }}">
                                            {{ $publishingHouse['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3 show-for-digital-product">
                            <div class="form-group">
                                <label for="digital-product-type-input" class="form-label">
                                    {{ translate("delivery_type") }}
                                    <span class="input-required-icon">*</span>
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{
                                        translate('for_Ready_Product_deliveries,_customers_can_pay_&_instantly_download_pre-uploaded_digital_products').' '.
                                        translate('For_Ready_After_Sale_deliveries,_customers_pay_first_then_admin_uploads_the_digital_products_that_become_available_to_customers_for_download') }}"
                                          data-bs-title="{{
                                        translate('for_Ready_Product_deliveries,_customers_can_pay_&_instantly_download_pre-uploaded_digital_products').' '.
                                        translate('For_Ready_After_Sale_deliveries,_customers_pay_first_then_admin_uploads_the_digital_products_that_become_available_to_customers_for_download') }}">
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <div class="select-wrapper">
                                    <select name="digital_product_type" id="digital-product-type-input" class="form-select"
                                            required>
                                        <option value="ready_after_sell">{{ translate("ready_After_Sell") }}</option>
                                        <option value="ready_product">{{ translate("ready_Product") }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="form-label d-flex justify-content-between gap-2">
                                    <span class="d-flex align-items-center gap-2">
                                        {{ translate('product_SKU') }}
                                        <span class="input-required-icon">*</span>
                                        <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                              aria-label="{{ translate('create_a_unique_product_code_by_clicking_on_the_Generate_Code_button') }}"
                                              data-bs-title="{{ translate('create_a_unique_product_code_by_clicking_on_the_Generate_Code_button') }}">
                                            <i class="fi fi-sr-info"></i>
                                        </span>
                                    </span>
                                    <span
                                        class="style-one-pro cursor-pointer user-select-none text-primary action-onclick-generate-number"
                                        data-input="#generate_number">
                                        {{ translate('generate_code') }}
                                    </span>
                                </label>
                                <input type="text" minlength="6" id="generate_number" name="code"
                                       class="form-control" value="{{ old('code') }}"
                                       placeholder="{{ translate('ex').': 161183'}}" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 show-for-physical-product">
                            <div class="form-group">
                                <label class="form-label">{{ translate('unit') }}</label>
                                <div class="select-wrapper">
                                    <select class="form-select" name="unit">
                                        @foreach (units() as $unit)
                                            <option value="{{ $unit }}" {{ old('unit') == $unit ? 'selected' : '' }}>
                                                {{ $unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label d-flex align-items-center gap-2">
                                    {{ translate('search_tags') }}
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('add_the_product_search_tag_for_this_product_that_customers_can_use_to_search_quickly') }}"
                                          data-bs-title="{{ translate('add_the_product_search_tag_for_this_product_that_customers_can_use_to_search_quickly') }}">
                                          <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <input type="text" class="form-control" placeholder="{{ translate('enter_tag') }}"
                                       name="tags" data-role="tagsinput">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="fi fi-sr-user"></i>
                        <h3 class="mb-0">{{ translate('pricing_&_others') }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-4 align-items-end">
                        <div class="col-md-6 col-lg-4 col-xl-3 d-none">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ translate('purchase_price') }}
                                    ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('add_the_purchase_price_for_this_product') }}."
                                          data-bs-title="{{ translate('add_the_purchase_price_for_this_product') }}."
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <input type="number" min="0" step="0.01"
                                       placeholder="{{ translate('purchase_price') }}"
                                       value="{{ old('purchase_price') }}" name="purchase_price"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ translate('unit_price') }}
                                    ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('set_the_selling_price_for_each_unit_of_this_product._This_Unit_Price_section_would_not_be_applied_if_you_set_a_variation_wise_price') }}"
                                          data-bs-title="{{ translate('set_the_selling_price_for_each_unit_of_this_product._This_Unit_Price_section_would_not_be_applied_if_you_set_a_variation_wise_price') }}"
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>

                                <input type="number" min="0" step="0.01"
                                       placeholder="{{ translate('unit_price') }}" name="unit_price"
                                       value="{{ old('unit_price') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3" id="minimum_order_qty">
                            <div class="form-group">
                                <label class="form-label" for="minimum_order_qty">
                                    {{ translate('minimum_order_qty') }}
                                    ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})
                                    <span class="input-required-icon">*</span>
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('set_the_minimum_order_quantity_that_customers_must_choose._Otherwise,_the_checkout_process_would_not_start') }}."
                                          data-bs-title="{{ translate('set_the_minimum_order_quantity_that_customers_must_choose._Otherwise,_the_checkout_process_would_not_start') }}."
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <input type="number" min="1" value="1" step="1"
                                       placeholder="{{ translate('minimum_order_quantity') }}" name="minimum_order_qty"
                                       id="minimum_order_qty" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 show-for-physical-product" id="quantity">
                            <div class="form-group">
                                <label class="form-label" for="current_stock">
                                    {{ translate('current_stock_qty') }}
                                    <span class="input-required-icon">*</span>
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('add_the_Stock_Quantity_of_this_product_that_will_be_visible_to_customers') }}."
                                          data-bs-title="{{ translate('add_the_Stock_Quantity_of_this_product_that_will_be_visible_to_customers') }}."
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>

                                <input type="number" min="0" value="0" step="1"
                                       placeholder="{{ translate('quantity') }}" name="current_stock" id="current_stock"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="form-label" for="product-discount-type">
                                    {{ translate('discount_Type') }}
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('if_Flat,_discount_amount_will_be_set_as_fixed_amount._If_Percentage,_discount_amount_will_be_set_as_percentage') }}."
                                          data-bs-title="{{ translate('if_Flat,_discount_amount_will_be_set_as_fixed_amount._If_Percentage,_discount_amount_will_be_set_as_percentage') }}."
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <select class="form-control product-discount-type" name="discount_type" id="product-discount-type">
                                    <option value="flat">{{ translate('flat') }}</option>
                                    <option value="percent">{{ translate('percent') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="form-label" for="discount">
                                    {{ translate('discount_amount') }}
                                    <span class="discount-amount-symbol" data-percent="%"
                                          data-currency="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}">
                                        ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})
                                    </span>
                                    <span class="input-required-icon">*</span>
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('add_the_discount_amount_in_percentage_or_a_fixed_value_here') }}."
                                          data-bs-title="{{ translate('add_the_discount_amount_in_percentage_or_a_fixed_value_here') }}."
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <input type="number" min="0" value="0" step="0.01"
                                       placeholder="{{ translate('ex: 5') }}"
                                       name="discount" id="discount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="form-label" for="tax">
                                    {{ translate('tax_amount') }}(%)
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('set_the_Tax_Amount_in_percentage_here') }}."
                                          data-bs-title="{{ translate('set_the_Tax_Amount_in_percentage_here') }}."
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>

                                <input type="number" min="0" step="0.01"
                                       placeholder="{{ translate('ex: 5') }}" name="tax" id="tax"
                                       value="{{ old('tax') ?? 0 }}" class="form-control">
                                <input name="tax_type" value="percent" class="d-none">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="form-label" for="tax_model">
                                    {{ translate('tax_calculation') }}
                                    <span class="input-required-icon">*</span>
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('set_the_tax_calculation_method_from_here.').' '.translate('select_Include_with_product_to_combine_product_price_and_tax_on_the_checkout.').' '.translate('pick_Exclude_from_product_to_display_product_price_and_tax_amount_separately.') }}"
                                          data-bs-title="{{ translate('set_the_tax_calculation_method_from_here.').' '.translate('select_Include_with_product_to_combine_product_price_and_tax_on_the_checkout.').' '.translate('pick_Exclude_from_product_to_display_product_price_and_tax_amount_separately.') }}"
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <div class="select-wrapper">
                                    <select name="tax_model" id="tax_model" class="form-select" required>
                                        <option value="include">{{ translate("include_with_product") }}</option>
                                        <option value="exclude">{{ translate("exclude_with_product") }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 show-for-physical-product" id="shipping_cost">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ translate('shipping_cost') }}
                                    ({{getCurrencySymbol(currencyCode: getCurrencyCode()) }})
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          aria-label="{{ translate('set_the_shipping_cost_for_this_product_here._Shipping_cost_will_only_be_applicable_if_product-wise_shipping_is_enabled.') }}"
                                          data-bs-title="{{ translate('set_the_shipping_cost_for_this_product_here._Shipping_cost_will_only_be_applicable_if_product-wise_shipping_is_enabled.') }}"
                                    >
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <input type="number" min="0" value="0" step="1"
                                       placeholder="{{ translate('shipping_cost') }}" name="shipping_cost"
                                       class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6 show-for-physical-product" id="shipping_cost_multi">
                            <div class="form-group">
                                <div
                                    class="form-control min-h-40 d-flex align-items-center flex-wrap justify-content-between gap-2">
                                    <label class="form-label mb-0"
                                           for="shipping_cost">{{ translate('shipping_cost_multiply_with_quantity') }}
                                        <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                              title="{{ translate('if_enabled,_the_shipping_charge_will_increase_with_the_product_quantity') }}">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                    </label>
                                    <div>
                                        <label class="switcher">
                                            <input type="checkbox" class="switcher_input" name="multiply_qty">
                                            <span class="switcher_control"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 rest-part show-for-physical-product">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="fi fi-sr-user"></i>
                        <h3 class="mb-0">{{ translate('product_variation_setup') }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-4 align-items-end">
                        <div class="col-md-6">
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <label for="colors" class="text-dark mb-0">
                                    {{ translate('select_colors') }} :
                                </label>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" id="product-color-switcher"
                                           value="{{ old('colors_active') }}"
                                           name="colors_active">
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                            <select class="custom-select color-var-select" name="colors[]" multiple="multiple"
                                    id="colors-selector" disabled>
                                @foreach ($colors as $color)
                                    <option value="{{ $color->code }}" data-color="{{ $color->code }}">
                                        {{ $color['name'] }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-6">
                            <label for="choice_attributes" class="form-label">
                                {{ translate('select_attributes') }} :
                            </label>
                            <select class="custom-select"
                                    name="choice_attributes[]" id="choice_attributes" multiple="multiple"
                                    data-placeholder="{{ translate('choose_attributes') }}">
                                <option></option>
                                @foreach ($attributes as $key => $a)
                                    <option value="{{ $a['id'] }}">
                                        {{ $a['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mt-2 mb-2">
                            <div class="row customer-choice-options-container my-0 gy-4" id="customer-choice-options-container"></div>
                            <div class="form-group sku_combination py-2" id="sku_combination"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 rest-part show-for-digital-product">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="fi fi-sr-user"></i>
                        <h3 class="mb-0">{{ translate('product_variation_setup') }}</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2" id="digital-product-type-choice-section">
                        <div class="col-sm-6 col-md-4 col-xxl-3">
                            <div class="multi--select">
                                <label class="form-label">{{ translate('File_Type') }}</label>
                                <select class="custom-select" name="file-type" multiple
                                        id="digital-product-type-select">
                                    @foreach($digitalProductFileTypes as $FileType)
                                        <option value="{{ $FileType }}">{{ translate($FileType) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 rest-part" id="digital-product-variation-section"></div>

            <div class="mt-3 rest-part">
                <div class="product-image-wrapper row g-4">
                    <div class="col-lg-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex flex-column gap-20">
                                    <div>
                                        <label for="" class="form-label fw-semibold mb-1">
                                            {{ translate('product_thumbnail') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <span
                                                class="badge text-bg-info badge-info badge-lg">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                                  aria-label="{{ translate('add_your_products_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB"
                                                  data-bs-title="{{ translate('add_your_products_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB"
                                            >
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="upload-file">
                                        <input type="file" name="image" class="upload-file__input single_file_input"
                                               accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                               value="" required>
                                        <button type="button"
                                                class="remove_btn btn btn-danger btn-circle w-20 h-20 fs-8">
                                            <i class="fi fi-sr-cross"></i>
                                        </button>
                                        <label
                                            class="upload-file__wrapper">
                                            <div class="upload-file-textbox text-center">
                                                <img width="34" height="34" class="svg"
                                                     src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}"
                                                     alt="image upload">
                                                <h6 class="mt-1 fw-medium lh-base text-center">
                                                    <span class="text-info">{{ translate('Click to upload') }}</span>
                                                    <br>
                                                    {{ translate('or drag and drop') }}
                                                </h6>
                                            </div>
                                            <img class="upload-file-img" loading="lazy" src="" data-default-src=""
                                                 alt="">
                                        </label>
                                        <div class="overlay">
                                            <div class="d-flex gap-10 justify-content-center align-items-center h-100">
                                                <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                    <i class="fi fi-sr-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                    <i class="fi fi-rr-camera"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9 color_image_column">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <label for="name" class="form-label fw-bold mb-0">
                                                {{ translate('colour_wise_product_image') }}
                                                <span class="input-required-icon">*</span>
                                            </label>
                                            <span
                                                class="badge badge-info text-bg-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                                  aria-label="{{ translate('add_color-wise_product_images_here') }}."
                                                  data-bs-title="{{ translate('add_color-wise_product_images_here') }}."
                                            >
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </div>

                                    </div>
                                    <p class="text-muted">
                                        {{ translate('must_upload_colour_wise_images_first.') }}
                                        {{ translate('Colour_is_shown_in_the_image_section_top_right') }}
                                    </p>

                                    <div id="color-wise-image-section" class="d-flex justify-content-start flex-wrap gap-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9 additional-image-column-section">
                        <div class="item-2 h-100">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <label for="name"
                                                   class="form-label fw-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                            <span
                                                class="badge badge-info text-bg-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                                  aria-label="{{ translate('upload_any_additional_images_for_this_product_from_here') }}."
                                                  data-bs-title="{{ translate('upload_any_additional_images_for_this_product_from_here') }}."
                                            >
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </div>

                                    </div>
                                    <p class="text-muted">{{ translate('upload_additional_product_images') }}</p>
                                    <div class="d-flex flex-column" id="additional_Image_Section">
                                        <div class="position-relative">
                                            <div class="multi_image_picker d-flex gap-20 pt-20"
                                                 data-ratio="1/1"
                                                 data-field-name="images[]"
                                            >
                                                <div>
                                                    <div class="imageSlide_prev">
                                                        <div
                                                            class="d-flex justify-content-center align-items-center h-100">
                                                            <button
                                                                type="button"
                                                                class="btn btn-circle border-0 bg-primary text-white">
                                                                <i class="fi fi-sr-angle-left"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="imageSlide_next">
                                                        <div
                                                            class="d-flex justify-content-center align-items-center h-100">
                                                            <button
                                                                type="button"
                                                                class="btn btn-circle border-0 bg-primary text-white">
                                                                <i class="fi fi-sr-angle-right"></i>
                                                            </button>
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

                    <div class="col-md-3">
                        <div class="item-1 show-for-digital-product h-100">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                            <div>
                                                <label for="name"
                                                       class="form-label text-capitalize fw-bold mb-0">{{ translate('Product_Preview_File') }}</label>
                                                <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                                      title="{{ translate('upload_a_suitable_file_for_a_short_product_preview.') }} {{ translate('this_preview_will_be_common_for_all_variations.') }}">
                                                    <i class="fi fi-sr-info"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-muted">{{ translate('Upload_a_short_preview') }}.</p>
                                    </div>
                                    <div class="image-uploader">
                                        <input type="file" name="preview_file" class="image-uploader__zip"
                                               id="input-file">
                                        <div class="image-uploader__zip-preview">
                                            <img
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                class="mx-auto" width="50" alt="">
                                            <div class="image-uploader__title line-2">
                                                {{ translate('Upload_File') }}
                                            </div>
                                        </div>
                                        <span class="btn btn-outline-danger icon-btn collapse zip-remove-btn">
                                            <i class="fi fi-rr-trash"></i>
                                        </span>
                                    </div>
                                    <p class="text-muted mt-2 fs-12">
                                        {{ translate('Format') }} : {{ " pdf, mp4, mp3" }}
                                        <br>
                                        {{ translate('image_size') }} : {{ translate('max') }} {{ "10 MB" }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="fi fi-sr-user"></i>
                        <h3 class="mb-0">{{ translate('product_video') }}</h3>
                        <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                              aria-label="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}."
                              data-bs-title="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}."
                        >
                            <i class="fi fi-sr-info"></i>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label mb-0">
                            {{ translate('youtube_video_link') }}
                        </label>
                        <span class="text-info"> ({{ translate('optional_please_provide_embed_link_not_direct_link') }}.)</span>
                    </div>
                    <input type="text" name="video_url"
                           placeholder="{{ translate('ex').': https://www.youtube.com/embed/5R06LRdUCSE' }}"
                           class="form-control" required>
                </div>
            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="fi fi-sr-user"></i>
                        <h3 class="mb-0">
                            {{ translate('seo_section') }}
                            <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                  data-bs-placement="top"
                                  title="{{ translate('add_meta_titles_descriptions_and_images_for_products').', '.translate('this_will_help_more_people_to_find_them_on_search_engines_and_see_the_right_details_while_sharing_on_other_social_platforms') }}">
                                <i class="fi fi-sr-info"></i>
                            </span>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">
                                    {{ translate('meta_Title') }}
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          data-bs-placement="top"
                                          title="{{ translate('add_the_products_title_name_taglines_etc_here').' '.translate('this_title_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') .' [ '. translate('character_Limit') }} : 100 ]">
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <input type="text" name="meta_title" placeholder="{{ translate('meta_Title') }}"
                                       class="form-control" id="meta_title">
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    {{ translate('meta_Description') }}
                                    <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                          data-bs-placement="top"
                                          title="{{ translate('write_a_short_description_of_the_InHouse_shops_product').' '.translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') .' [ '. translate('character_Limit') }} : 160 ]">
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <textarea rows="4" type="text" name="meta_description" id="meta_description"
                                          class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex justify-content-center">
                                <div class="d-flex flex-column gap-20">
                                    <div>
                                        <label for="meta_Image" class="form-label fw-semibold mb-1">
                                            {{ translate('meta_Image') }}
                                            <span
                                                class="badge badge-info text-bg-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>
                                            <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                                  aria-label="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}."
                                                  data-bs-title="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}."
                                            >
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="upload-file">
                                        <input type="file" name="meta_image"
                                               class="upload-file__input single_file_input"
                                               id="meta_image_input"
                                               accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                               value=""
                                        >
                                        <label
                                            class="upload-file__wrapper ratio-2-1">
                                            <div class="upload-file-textbox text-center">
                                                <img width="34" height="34" class="svg"
                                                     src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}"
                                                     alt="image upload">
                                                <h6 class="mt-1 fw-medium lh-base text-center">
                                                    <span class="text-info">{{ translate('Click to upload') }}</span>
                                                    <br>
                                                    {{ translate('or_drag_and_drop') }}
                                                </h6>
                                            </div>
                                            <img class="upload-file-img" loading="lazy" src="" data-default-src=""
                                                 alt="">
                                        </label>
                                        <div class="overlay">
                                            <div class="d-flex gap-10 justify-content-center align-items-center h-100">
                                                <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                    <i class="fi fi-sr-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                    <i class="fi fi-rr-camera"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin-views.product.partials._seo-section')
                </div>
            </div>

            <div class="d-flex justify-content-end flex-wrap gap-3 mt-3 mx-1">
                <button type="reset" class="btn btn-secondary px-5">{{ translate('reset') }}</button>
                <button type="button" class="btn btn-primary px-5 product-add-requirements-check">
                    {{ translate('submit') }}
                </button>
            </div>
        </form>
    </div>

    @include("layouts.admin.partials.offcanvas._add-new-product")

{{--    <span id="route-admin-products-sku-combination" data-url="{{ route('admin.products.sku-combination') }}"></span>--}}
    <span id="route-admin-products-digital-variation-combination"
          data-url="{{ route('admin.products.digital-variation-combination') }}"></span>
    <span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>

    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
    <span id="message-no-word" data-text="{{ translate('no') }}"></span>
    <span id="message-want-to-add-or-update-this-product" data-text="{{ translate('want_to_add_this_product') }}"></span>
    <span id="message-please-only-input-png-or-jpg" data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-product-added-successfully" data-text="{{ translate('product_added_successfully') }}"></span>
    <span id="message-discount-will-not-larger-then-variant-price"
          data-text="{{ translate('the_discount_price_will_not_larger_then_Variant_Price') }}"></span>
    <span id="system-currency-code" data-value="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor-init.js') }}"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-colors-img.js') }}"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/product-add-update.js') }}"></script>
@endpush
