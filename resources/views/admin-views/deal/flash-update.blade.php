@extends('layouts.admin.app')

@section('title', translate('flash_Deal_Update'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/flash_deal.png') }}" alt="">
                {{ translate('flash_deals_update') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.deal.update-data',[$deal['id']]) }}" method="post"
                              class="text-start onsubmit-disable-action-button"
                              enctype="multipart/form-data">
                            @csrf
                            @php($language = getWebConfig(name:'pnc_language'))
                            @php($defaultLanguage = 'en')
                            @php($defaultLanguage = $language[0])
                            <div class="position-relative nav--tab-wrapper mb-4">
                                <ul class="nav nav-pills nav--tab lang_tab" id="pills-tab" role="tablist">
                                    @foreach($language as $lang)
                                        <li class="nav-item px-0" role="presentation">
                                            <a class="nav-link px-2 {{ $lang == $defaultLanguage ? 'active':'' }}" id="{{ $lang }}-link" data-bs-toggle="pill" href="#{{ $lang }}-form" role="tab" aria-selected="true">
                                                {{getLanguageName($lang).'('.strtoupper($lang).')' }}
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
                            <div class="row g-4 mb-4">
                                <div class="col-lg-6">
                                   <div class="h-100">
                                        <div class="tab-content mb-4" id="pills-tabContent">
                                            @foreach($language as $lang)
                                                <?php
                                                if (count($deal['translations'])) {
                                                    $translate = [];
                                                    foreach ($deal['translations'] as $t) {
                                                        if ($t->locale == $lang && $t->key == "title") {
                                                            $translate[$lang]['title'] = $t->value;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active':'' }}" id="{{ $lang }}-form" role="tabpanel" aria-labelledby="{{ $lang }}-form-tab">
                                                    <div class="form-group">
                                                        <input type="text" name="deal_type" value="flash_deal" class="d-none">
                                                        <label for="name" class="form-label">{{ translate('title') }}
                                                            ({{strtoupper($lang) }})</label>
                                                        <input type="text" name="title[]" class="form-control" id="title"
                                                            value="{{ $lang==$defaultLanguage?$deal['title']:($translate[$lang]['title']??'') }}"
                                                            placeholder="{{ translate('ex').':'.' '.translate('LUX') }}"
                                                                {{ $lang == $defaultLanguage? 'required':'' }}>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}" id="lang">
                                            @endforeach

                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="name" class="form-label">{{ translate('start_date') }}</label>
                                            <input type="date" value="{{date('Y-m-d',strtotime($deal['start_date'])) }}"
                                                name="start_date" id="start-date-time" required
                                                class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="name" class="form-label">{{ translate('end_date') }}</label>
                                            <input type="date" value="{{date('Y-m-d', strtotime($deal['end_date'])) }}"
                                                name="end_date" id="end-date-time" required
                                                class="form-control">
                                        </div>
                                   </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-center align-items-center bg-section rounded-8 p-20 w-100 h-100">
                                        <div class="d-flex flex-column gap-30 w-100">
                                            <div class="text-center">
                                                <label for="" class="form-label fw-semibold mb-0">
                                                    {{translate('upload_image') }}
                                                    <span class="text-info-dark">( {{translate('ratio').' '.'5:1' }} )</span>
                                                </label>
                                            </div>
                                            <div class="upload-file">
                                                <input type="file" name="image" id="custom-file-upload" class="upload-file__input single_file_input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"  value="" {{ $deal->banner_full_url ? '':'required' }}>
                                                <div
                                                    class="upload-file__wrapper ratio-5-1">
                                                    <div class="upload-file-textbox text-center">
                                                        <img width="34" height="34" class="svg" src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}" alt="image upload">
                                                        <h6 class="mt-1 fw-medium lh-base text-center">
                                                            <span class="text-info">{{ translate('Click to upload') }}</span>
                                                            <br>
                                                            {{ translate('or drag and drop') }}
                                                        </h6>
                                                    </div>
                                                    <img class="upload-file-img" loading="lazy"
                                                        src="{{ getStorageImages(path: $deal->banner_full_url , type: 'backend-basic') }}"
                                                        data-default-src="{{ getStorageImages(path: $deal->banner_full_url , type: 'backend-basic') }}"
                                                        alt="{{ translate('banner_image') }}">
                                                </div>
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
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                        class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary px-4">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/deal.js') }}"></script>
@endpush
