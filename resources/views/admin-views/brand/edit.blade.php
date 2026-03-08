@extends('layouts.admin.app')

@section('title', translate('brand_Update'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand.png') }}" alt="">
                {{ translate('brand_Update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.brand.update', [$brand['id']]) }}" method="post"
                              enctype="multipart/form-data" class="brand-setup-form">
                            @csrf

                            <div class="table-responsive w-auto overflow-y-hidden mb-4">
                                <div class="position-relative nav--tab-wrapper">
                                    <ul class="nav nav-pills nav--tab lang_tab" id="pills-tab" role="tablist">
                                        @foreach($language as $lang)
                                        <li class="nav-item px-0">
                                            <a data-bs-toggle="pill" data-bs-target="#{{ $lang }}-form" role="tab" class="nav-link px-2 {{ $lang == $defaultLanguage ? 'active' : '' }}" id="{{ $lang }}-link">
                                                {{ucfirst(getLanguageName($lang)).'('.strtoupper($lang).')' }}
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

                            <div class="row gy-4 mb-4">
                                <div class="col-md-6">
                                    <div class="tab-content" id="pills-tabContent">
                                        @foreach($language as $lang)
                                        <?php
                                            if (count($brand['translations'])) {
                                                $translate = [];
                                                foreach ($brand['translations'] as $translations) {
                                                    if ($translations->locale == $lang && $translations->key == "name") {
                                                        $translate[$lang]['name'] = $translations->value;
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active' : '' }}" id="{{ $lang }}-form" aria-labelledby="{{ $lang }}-link" role="tabpanel">
                                                <label class="form-label" for="exampleFormControlInput1">
                                                    {{ translate('brand_Name') }}
                                                    <span class="text-danger">*</span>
                                                    ({{ strtoupper($lang) }})
                                                </label>
                                                <input type="text" name="name[]"
                                                   value="{{ $lang == $defaultLanguage ? $brand['name'] : ($translate[$lang]['name'] ?? '') }}"
                                                   class="form-control" id="name"
                                                   placeholder="{{ translate('ex') }} : {{ translate('LUX') }}" {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            {{ translate('image_alt_text') }}
                                        </label>
                                        <input type="text" name="image_alt_text" class="form-control" value="{{ $brand['image_alt_text'] }}"
                                               placeholder="{{ translate('ex').' : '.translate('apex_Brand') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4 shadow-none">
                                <div class="card-body">
                                    <div class="d-flex flex-column gap-20">
                                        <div class="text-center">
                                            <label for="" class="form-label fw-semibold mb-1">
                                                {{ translate('image') }}
                                                <small class="text-danger">
                                                    {{'('.translate('size').': 1:1)' }}
                                                </small>
                                            </label>
                                        </div>
                                        <div class="upload-file">
                                            <input type="file" name="image" id="brand-image" class="upload-file__input single_file_input"
                                                accept=".webp, .jpg, .jpeg, .png"  value="" >
                                            <label
                                                class="upload-file__wrapper">
                                                <div class="upload-file-textbox text-center">
                                                    <img width="34" height="34" class="svg" src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}" alt="image upload">
                                                    <h6 class="mt-1 fw-medium lh-base text-center">
                                                        <span class="text-info">
                                                            {{ translate('Click_to_upload') }}
                                                        </span>
                                                        <br>
                                                        {{ translate('or drag and drop') }}
                                                    </h6>
                                                </div>
                                                <img class="upload-file-img" loading="lazy" src="{{ getStorageImages($brand->image_full_url, 'backend-brand') ?? '' }}" data-default-src="{{ getStorageImages($brand->image_full_url, 'backend-brand') ?? '' }}" alt="">
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
                                        <p class="fs-10 mb-0 text-center">
                                            {{ translate('image_format') }} : {{ "jpg, png, jpeg, webp" }}
                                            <br>
                                            {{ translate('image_size') }} : {{ translate('max') }} {{ "2 MB" }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset" class="btn btn-secondary px-4">
                                    {{ translate('reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    {{ translate('update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('.brand-setup-form').on('reset', function () {
            window.location.reload()
        });
    </script>
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/products-management.js') }}"></script>
@endpush
