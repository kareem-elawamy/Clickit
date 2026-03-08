@extends('layouts.admin.app')

@section('title', translate(request('product-gallery') == 1 ? 'product_Add' : 'product_Edit'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}"
                     alt="{{ translate('product_Edit') }}">
                {{ translate(request('product-gallery') == 1 ?'product_Add' : 'product_Edit') }}
            </h2>
        </div>

        <form class="product-form text-start" enctype="multipart/form-data" id="product_form"
              action="{{ request('product-gallery') == 1 ? route('admin.products.add') : route('admin.products.update', $product->id) }}" method="POST">
            @csrf
            @include("admin-views.product.update._title-description")
            @include("admin-views.product.update._general-setup")
            @include("admin-views.product.update._pricing-others")
            @include("admin-views.product.update._product-variation-setup")
            @include("admin-views.product.update._product-images")
            @include("admin-views.product.update._product-video")
            @include("admin-views.product.update._seo-section")

            <div class="d-flex justify-content-end flex-wrap gap-3 mt-3 mx-1">
                <button type="button" class="btn btn-primary px-5 product-add-requirements-check">
                    @if($product->request_status == 2)
                        {{ translate('update_&_Publish') }}
                    @else
                        {{ translate(request('product-gallery') ? 'submit' : 'update') }}
                    @endif
                </button>
            </div>
        </form>
    </div>

    <span id="product-add-update-messages"
          data-are-you-sure="{{ translate('are_you_sure') }}"
          data-want-to-add="{{ translate('want_to_update_this_product') }} ?"
          data-yes-word="{{ translate('yes') }}"
          data-no-word="{{ translate('no') }}"
    ></span>
    <span id="message-product-added-successfully" data-text="{{ translate('product_update_successfully') }}"></span>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-click-to-upload" data-text="{{ translate('click_to_upload') }}"></span>
    <span id="message-drag-and-drop" data-text="{{ translate('Or_drag_and_drop') }}"></span>

    <span id="route-admin-products-sku-combination" data-url="{{ route('admin.products.sku-combination') }}"></span>
    <span id="route-admin-products-digital-variation-combination" data-url="{{ route('admin.products.digital-variation-combination') }}"></span>
    <span id="route-admin-products-digital-variation-file-delete" data-url="{{ route('admin.products.digital-variation-file-delete') }}"></span>
    <span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor-init.js') }}"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/product-add-update-utils.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/product-add-update.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/product-add-update-ajax.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/product-add-colors-img.js') }}"></script>

    <script>
        $(document).ready(function () {
            updateProductQuantity();
            setTimeout(function () {
                let category = $("#category_id").val();
                let sub_category = $("#sub-category-select").attr("data-id");
                let sub_sub_category = $("#sub-sub-category-select").attr("data-id");
                getRequestFunctionality('{{ route('admin.products.get-categories') }}?parent_id=' + category + '&sub_category=' + sub_category, 'sub-category-select', 'select');
                getRequestFunctionality('{{ route('admin.products.get-categories') }}?parent_id=' + sub_category + '&sub_category=' + sub_sub_category, 'sub-sub-category-select', 'select');
            }, 100)
        });

    </script>
@endpush
