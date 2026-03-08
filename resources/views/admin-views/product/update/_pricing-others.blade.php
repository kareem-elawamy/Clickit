<div class="card mt-3 rest-part">
    <div class="card-header">
        <div class="d-flex gap-2">
            <i class="fi fi-sr-user"></i>
            <h3 class="mb-0">{{ translate('pricing_&_others') }}</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="row gy-4 align-items-end">
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label class="form-label">
                        {{ translate('unit_price') }}
                        <span class="input-required-icon">*</span>
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
                           value="{{ usdToDefaultCurrency($product->unit_price) }}" class="form-control" required>
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
                    <input type="number" min="1" value="{{ $product->minimum_order_qty }}" step="1"
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

                    <input type="number" min="0" value="{{ $product->current_stock }}" step="1"
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
                        <option value="flat" {{ $product['discount_type']=='flat'?'selected':''}}>
                            {{ translate('flat') }}
                        </option>
                        <option value="percent" {{ $product['discount_type']=='percent'?'selected':''}}>
                            {{ translate('percent') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label class="form-label" for="discount">
                        {{ translate('discount_amount') }}
                        <span class="discount-amount-symbol" data-percent="%"
                              data-currency="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}">
                            ({{ $product->discount_type == 'flat' ? getCurrencySymbol(currencyCode: getCurrencyCode()) : '%' }})
                        </span>
                        <span class="input-required-icon">*</span>
                        <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                              aria-label="{{ translate('add_the_discount_amount_in_percentage_or_a_fixed_value_here') }}."
                              data-bs-title="{{ translate('add_the_discount_amount_in_percentage_or_a_fixed_value_here') }}."
                        >
                            <i class="fi fi-sr-info"></i>
                        </span>
                    </label>
                    <input type="number" min="0"
                           value="{{ $product->discount_type == 'flat' ? usdToDefaultCurrency($product->discount) : $product->discount }}"
                           step="any"
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
                           value="{{ $product->tax ?? 0 }}" class="form-control">
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
                            <option value="include" {{ $product->tax_model == 'include' ? 'selected':'' }}>
                                {{ translate("include_with_product") }}
                            </option>
                            <option value="exclude" {{ $product->tax_model == 'exclude' ? 'selected':'' }}>
                                {{ translate("exclude_with_product") }}
                            </option>
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
                    <input type="number" min="0" value="{{ usdToDefaultCurrency($product->shipping_cost) }}" step="any"
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
                                <input type="checkbox" class="switcher_input" name="multiply_qty"
                                    {{ $product['multiply_qty'] == 1 ? 'checked' : '' }}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
