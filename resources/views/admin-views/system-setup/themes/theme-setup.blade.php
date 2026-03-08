@extends('layouts.admin.app')

@section('title', translate('Theme_Setup'))

@section('content')
    <div class="content container-fluid">
        <h1 class="mb-3 mb-sm-20">
            {{ translate('Theme_Setup') }}
        </h1>

        <div class="card mb-3">
            <div class="card-body">
                <form enctype="multipart/form-data" id="addon-upload-form" action="{{ route('admin.system-setup.theme.install') }}" method="POST">
                    @csrf
                    <div class="d-flex flex-column gap-3 gap-sm-20">
                        <div class="">
                            <h2>{{ translate('Upload_Theme') }}</h2>
                            <p class="mb-0 fs-12">
                                {{ translate('here_you_can_upload_themes_for_customer_website_to_give_customer_better_visual') }}
                            </p>
                        </div>

                        <div class="row g-4">

                            @php($conditionOne=checkUploadMaxFileSizeLimit(limit: 20))
                            @php($conditionTwo=checkPostMaxFileSizeLimit(limit: 20))

                            <div class="col-lg-6">
                                <div class="bg-section rounded-8 p-12 p-sm-20">
                                    <div class="file-upload-parent {{ $conditionOne && $conditionTwo ? "" : 'disabled' }}">
                                        <div class="custom-file-upload mb-4">
                                            <input type="file" accept=".zip" data-max-file-size="50MB" name="theme_upload" id="input-file" />
                                            <div class="text-center user-select-none">
                                                <div class="mb-20">
                                                    <i class="fi fi-rr-cloud-upload-alt fs-1 text-black-50"></i>
                                                </div>
                                                <p class="mb-0 fs-14 mb-1">
                                                    {{ translate('Select_a_file_or') }}
                                                    <span class="fw-semibold">
                                                        {{ translate('Drag_&_Drop') }}
                                                    </span>
                                                    {{ translate('here') }}
                                                </p>
                                                <div class="mb-0 fs-12">
                                                    {{ translate('total_file_size_no_more_than_50mb') }}
                                                </div>
                                                <div class="btn btn-outline-primary mt-30 trigger_input_btn">
                                                    {{ translate('Select_File') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="file-preview-list d-flex flex-column gap-4"></div>
                                        <div id="file-upload-config" data-icon-src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/icons/file-view.png') }}"></div>

                                        <div class="mt-4 d--none progress-bar-container">
                                            <div class="d-flex justify-content-between mb-1 flex-wrap gap-2">
                                                <span>{{ translate('Progress') }}...</span>
                                                <span class="upload-progress-label"></span>
                                            </div>
                                            <div class="progress" role="progressbar" aria-label="Success" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-striped bg-success upload-progress-bar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="bg-warning bg-opacity-10 fs-12 px-20 py-3 text-dark rounded-8 h-100 d-flex justify-content-center flex-column">
                                    <h3 class="text-info-dark">{{ translate('instructions') }}</h3>
                                    <ul class="m-0 ps-20 d-flex flex-column gap-1 text-body">
                                        <li>
                                            {{ translate('please_make_sure').','.translate('your_server_php').'"'.translate('upload_max_filesize').'"'.translate('value_is_grater_or_equal_to_20MB.').' '.translate('current_value_is').'-'.ini_get('upload_max_filesize').'B' }}
                                        </li>
                                        <li>
                                            {{ translate('please_make_sure').','.translate('your_server_php').'"'.translate('post_max_size').'"'.translate('value_is_grater_or_equal_to_20MB.').' '.translate('current_value_is') .'-'.ini_get('post_max_size').'B'}}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex flex-wrap justify-content-end gap-3">
                                    <button type="reset" class="btn btn-secondary px-sm-4 min-w-120">
                                        {{ translate('cancel') }}
                                    </button>
                                    <button type="{{ getDemoModeFormButton(type: 'button') }}"
                                            {{ $conditionOne && $conditionTwo ? '' : 'disabled' }}
                                            class="btn btn-primary px-sm-4 min-w-120 {{ getDemoModeFormButton(type: 'class') }}">
                                        {{ translate('save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="mb-3 mb-sm-20 d-flex flex-wrap gap-3 justify-content-between align-items-center">
                    <div>
                        <h2>{{ translate('Available_Themes') }}</h2>
                        <p class="mb-0 fs-12">
                            {{ translate('select_the_theme_you_want_to_use_for_your_system') }}
                        </p>
                    </div>
                    @if(count($themes) < 2)
                    <a class="btn bg-secondary text-white lh-1 px-3 rounded-45"
                       target="_blank"
                       href="{{ 'https://codecanyon.net/item/6valley-multivendor-ecommerce-complete-ecommerce-mobile-app-web-and-admin-panel/31448597' }}">
                        {{ translate('Check_New_Themes') }}
                        <i class="fi fi-rr-up-right-from-square"></i>
                    </a>
                    @endif
                </div>

                <div class="row g-3">

                    @foreach($themes as $key => $theme)
                        @if(isset($theme['software_id']))
                            <div class="col-sm-6 col-xl-4">
                            <div class="card border shadow-none h-100 overflow-hidden {{ theme_root_path() == $key ? 'theme-active' : '' }}">
                                <div class="bg-section p-12 p-sm-20 d-flex justify-content-between gap-3 align-items-start">
                                    <div>
                                        <div class="d-flex gap-2 align-items-center mb-3">
                                            <h3 class="fw-bold mb-0">
                                                {{ ucwords(str_replace('_', ' ', $key =='default' ? 'default_theme' : $theme['name'] ?? '')) }}
                                            </h3>
                                            @if($theme['is_active'])
                                                <div class="text-white px-2 py-1 fs-12 lh-1 fw-semibold rounded bg-success">
                                                    {{ translate('Active') }}
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="text-info-dark mb-0">
                                            {{ translate('Version') }} {{ $theme['version'] ?? '1.0' }}
                                        </h5>
                                    </div>

                                    <div class="d-flex gap-2 gap-sm-3 align-items-center">
                                        @if(($key != 'default' && $key != 'theme_aster') && theme_root_path() != $key)
                                            <button class="btn btn-outline-danger bg-danger bg-opacity-10 icon-btn" data-bs-toggle="modal"
                                            data-bs-target="#deleteThemeModal_{{ $key }}">
                                                <i class="fi fi-sr-trash"></i>
                                            </button>
                                        @endif

                                        @if(theme_root_path() == $key)
                                            <input class="form-check-input radio--input radio--input_lg" type="radio" checked>
                                        @else
                                            <input class="form-check-input radio--input radio--input_lg theme-publish-status theme-publish-status-{{ $key }}"
                                                   type="radio" data-bs-toggle="modal"
                                                   data-bs-target="#shiftThemeModal_{{ $key }}">
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="ratio-3-2 border rounded-10">
                                        <?php
                                            if (DOMAIN_POINTED_DIRECTORY == 'public') {
                                                $themeImage = dynamicAsset(path: 'public/themes/'.$key.'/public/addon/'.($theme['image'] ?? ''));
                                            } else {
                                                $themeImage = dynamicAsset(path: 'resources/themes/'.$key.'/public/addon/'.$theme['image'] ?? '');
                                            }
                                        ?>
                                        <img class="img-fit rounded-10" alt=""
                                             src="{{ getStorageImages(path: null, type: 'backend-basic', source: $themeImage) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach

                    @if(count($themes) < 2)
                        <div class="col-12">
                            <div class="border border-dashed rounded-10 p-12 p-sm-20 d-flex flex-column justify-content-center align-items-center gap-3 gap-sm-20 h-100">
                                <img width="60" height="60" src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/explore-themes.png') }}" alt="">
                                <h3 class="mb-2">{{ translate('Explore_Other_Themes') }}</h3>
                                <p class="fs-12 mb-0 text-center">
                                    {{ translate('browse_our_themes_and_add_here_to_enhance_your_website_look_and_functionality.') }}
                                </p>
                                <a class="btn bg-secondary text-white lh-1 px-3 rounded-45"
                                   target="_blank"
                                   href="{{ 'https://codecanyon.net/item/6valley-multivendor-ecommerce-complete-ecommerce-mobile-app-web-and-admin-panel/31448597' }}">
                                    {{ translate('View_Themes') }}
                                    <i class="fi fi-rr-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    @include('admin-views.system-setup.themes.theme-activate-modal')
                </div>
            </div>
        </div>

        @include("admin-views.system-setup.themes._theme-modals")
    </div>

    <span id="get-theme-publish-route"
          data-action="{{ route('admin.system-setup.theme.publish') }}"></span>
    <span id="get-theme-delete-route"></span>
    <span id="get-notify-all-vendor-route-and-img-src"
          data-csrf="{{ csrf_token() }}"
          data-src="{{ dynamicAsset(path: 'public/assets/back-end/img/notify_success.png') }}"
          data-action="{{ route('admin.system-setup.theme.notify-all-the-vendors') }}">
    </span>

    @include("layouts.admin.partials.offcanvas._theme-setup")
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/addon.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/business-setting/theme-setup.js') }}"></script>
@endpush
