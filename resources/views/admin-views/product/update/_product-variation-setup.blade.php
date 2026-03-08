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
                               value="1" {{ count($product['colors']) > 0 ? 'checked' : '' }}
                               name="colors_active">
                        <span class="switcher_control"></span>
                    </label>
                </div>
                <select class="custom-select color-var-select" name="colors[]" multiple="multiple"
                        id="colors-selector-input" {{ count($product['colors']) > 0 ? '' : 'disabled' }}>
                    @foreach ($colors as $color)
                        <option value="{{ $color->code }}" data-color="{{ $color->code }}"
                            {{ in_array($color->code,$product['colors']) ? 'selected' : '' }}>
                            {{ $color['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="product-choice-attributes" class="form-label">
                    {{ translate('select_attributes') }} :
                </label>
                <select class="custom-select"
                        name="choice_attributes[]" id="product-choice-attributes" multiple="multiple"
                        data-placeholder="{{ translate('choose_attributes') }}">
                    <option></option>
                    @foreach ($attributes as $key => $attribute)
                        @if($product['attributes']!='null')
                            <option value="{{ $attribute['id'] }}"
                                {{ in_array($attribute->id, json_decode($product['attributes'], true)) ? 'selected' : '' }}>
                                {{ $attribute['name']}}
                            </option>
                        @else
                            <option value="{{ $attribute['id'] }}">{{ $attribute['name'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 mt-2 mb-2">
                <div class="row customer-choice-options-container mt-0 mb-4 gy-4 " id="customer-choice-options-container">
                    @include('admin-views.product.partials._choices', [
                        'choice_no' => json_decode($product['attributes']),
                        'choice_options' => json_decode($product['choice_options'],true)
                    ])
                </div>

                <div class="sku_combination table-responsive form-group mt-2" id="sku_combination">
                    @include('admin-views.product.partials._edit-sku-combinations', [
                        'combinations' => json_decode($product['variation'], true)
                    ])
                </div>
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
                    <select class="custom-select" name="file-type" multiple id="digital-product-type-select">
                        @foreach($digitalProductFileTypes as $FileType)
                            @if($product->digital_product_file_types)
                                <option value="{{ $FileType }}"
                                    {{ in_array($FileType, $product->digital_product_file_types) ? 'selected' : '' }}>
                                    {{ translate($FileType) }}
                                </option>
                            @else
                                <option value="{{ $FileType }}">{{ translate($FileType) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            @if($product->digital_product_file_types && count($product->digital_product_file_types) > 0)
                @foreach($product->digital_product_file_types as $digitalProductFileTypes)
                    <div class="col-sm-6 col-md-4 col-xxl-3 extension-choice-section">
                        <div class="form-group">
                            <input type="hidden" name="extensions_type[]"
                                   value="{{ $digitalProductFileTypes }}">
                            <label class="form-label">
                                {{ $digitalProductFileTypes }}
                            </label>
                            <input type="text" name="extensions[]" value="{{ $digitalProductFileTypes }}"
                                   hidden>
                            <div class="">
                                @if($product->digital_product_extensions && isset($product->digital_product_extensions[$digitalProductFileTypes]))
                                    <input type="text" class="form-control"
                                           name="extensions_options_{{ $digitalProductFileTypes }}[]"
                                           placeholder="{{ translate('enter_choice_values') }}"
                                           data-role="tagsinput"
                                           value="@foreach($product->digital_product_extensions[$digitalProductFileTypes] as $extensions){{ $extensions.','}}@endforeach"
                                           onchange="getUpdateDigitalVariationFunctionality()"
                                    >
                                @else
                                    <input type="text" class="form-control"
                                           name="extensions_options_{{ $digitalProductFileTypes }}[]"
                                           placeholder="{{ translate('enter_choice_values') }}"
                                           data-role="tagsinput"
                                           onchange="getUpdateDigitalVariationFunctionality()"
                                    >
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<div class="card mt-3 rest-part" id="digital-product-variation-section"></div>
