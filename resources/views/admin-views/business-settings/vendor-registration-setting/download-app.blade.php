@extends('layouts.admin.app')

@section('title', translate('download_app'))

@section('content')
    <div class="content container-fluid">
        @include('admin-views.business-settings.vendor-registration-setting.partial.inline-menu')

        <form action="{{route('admin.pages-and-media.vendor-registration-settings.download-app') }}" method="post"
              enctype="multipart/form-data">
            @csrf

            <div class="d-flex flex-column gap-3 gap-sm-20">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center gy-3 mb-3 mb-sm-20">
                            <div class="col-md-9">
                                <div>
                                    <h2>{{ translate('download_app_section') }}</h2>
                                    <p class="fs-12 mb-0">
                                        {{ translate('this_section_represent_about_your_vendor_app') }}.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div
                                    class="d-flex justify-content-between align-items-center gap-3 rounded px-20 py-3 user-select-none bg-section">
                                    <span class="fw-semibold text-dark">{{ translate('Status') }}</span>
                                    <label class="switcher"
                                           for="download-app-status">
                                        <input
                                            class="switcher_input custom-modal-plugin"
                                            type="checkbox" value="1" name="status"
                                            id="download-app-status"
                                            {{ isset($downloadVendorApp->status) && $downloadVendorApp?->status == 1 ? 'checked' : '' }}
                                            data-modal-type="input-change"
                                            data-on-image="{{ dynamicAsset(path: 'public/assets/backend/media/toggle-modal-icons/turn-on.svg') }}"
                                            data-off-image="{{ dynamicAsset(path: 'public/assets/backend/media/toggle-modal-icons/turn-off.svg') }}"
                                            data-on-title="{{translate('want_to_Turn_ON_this_status').'?'}}"
                                            data-off-title="{{translate('want_to_Turn_OFF_this_status').'?'}}"
                                            data-on-message="<p>{{ translate('once_you_turn_on_the_status_and_complete_the_setup')}}, {{ translate('_this_section_will_be_displayed_on_the_vendor_registration_page') }}</p>"
                                            data-off-message="<p>{{ translate('once_you_turn_off_the_status')}}, {{ translate('_this_section_won’t_be_displayed_on_the_vendor_registration_page') }}</p>"
                                            data-on-button-text="{{ translate('turn_on') }}"
                                            data-off-button-text="{{ translate('turn_off') }}">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-3 mb-4">
                            <div class="col-lg-8">
                                <div class="p-12 p-sm-20 bg-section rounded h-100">
                                    <div>
                                        <label class="form-label">
                                            {{ translate('section_title') }}
                                            <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                  aria-label="add title" data-bs-title="add title">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </label>
                                        <textarea class="form-control" name="title" rows="1"
                                                  placeholder="{{ translate('enter_title') }}"
                                                  data-maxlength="50">{{$downloadVendorApp?->title}}</textarea>
                                        <div class="d-flex justify-content-end">
                                            <span class="text-body-light">0/50</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">
                                            {{ translate('sub_title') }}
                                            <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                  aria-label="add sub title" data-bs-title="add sub title">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </label>
                                        <textarea class="form-control" name="sub_title" rows="2"
                                                  placeholder="{{ translate('enter_sub_title') }}"
                                                  data-maxlength="160">{{$downloadVendorApp?->sub_title}}</textarea>
                                        <div class="d-flex justify-content-end">
                                            <span class="text-body-light">0/160</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="p-12 p-sm-20 bg-section rounded h-100">
                                    <div class="d-flex flex-column gap-20">
                                        <div>
                                            <label for="" class="form-label fw-semibold mb-1">
                                                {{ translate('Header_Image') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <p class="fs-12 mb-0">{{ translate('upload_your_header_image') }}</p>
                                        </div>
                                        <div class="upload-file">
                                            @php($imagePath = imagePathProcessing(imageData:$downloadVendorApp?->image, path: 'vendor-registration-setting'))
                                            <input type="file" name="image" class="upload-file__input single_file_input"
                                                   accept=".webp, .jpg, .jpeg, .png"
                                                   value="{{ getStorageImages(path:imagePathProcessing(imageData: $downloadVendorApp?->image, path: 'vendor-registration-setting'),type: 'backend-basic') ?? '' }}" {{ !empty($imagePath['path']) ? '' : 'required' }}>
                                            <label class="upload-file__wrapper">
                                                <div
                                                    class="upload-file-textbox text-center {{ !empty($imagePath['path']) ? 'd-none' : '' }}">
                                                    <img width="34" height="34" class="svg"
                                                         src="{{dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}"
                                                         alt="image upload">
                                                    <h6 class="mt-1 fw-medium lh-base text-center">
                                                        <span
                                                            class="text-info">{{ translate('Click_to_upload') }}</span>
                                                        <br>
                                                        {{ translate('or_drag_and_drop') }}
                                                    </h6>
                                                </div>

                                                <img class="upload-file-img" id="view-header-logo" loading="lazy"
                                                     src="{{ !empty($imagePath['path']) ? getStorageImages(path:$imagePath,type: 'backend-basic') ?? '' :  '' }}"
                                                     data-default-src="{{ getStorageImages(path:imagePathProcessing(imageData: $downloadVendorApp?->image, path: 'vendor-registration-setting'),type: 'backend-basic') ?? '' }}"
                                                     alt="">
                                            </label>
                                            <div class="overlay">
                                                <div
                                                    class="d-flex gap-10 justify-content-center align-items-center h-100">
                                                    <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                        <i class="fi fi-sr-eye"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-info icon-btn edit_btn">
                                                        <i class="fi fi-rr-camera"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="fs-10 mb-0 text-center">
                                            {{ translate('jpg,_jpeg,_png,_image_size') }} : {{ translate('Max_2_MB') }}
                                            <span class="fw-medium text-dark">{{ "(1:1)" }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-end gap-3">
                            <button type="reset" class="btn btn-secondary px-4 w-120">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn-primary px-4 w-120">{{ translate('submit') }}</button>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-warning bg-opacity-10 fs-12 px-12 py-10 text-dark rounded d-flex gap-2 align-items-center">
                    <i class="fi fi-sr-info text-warning"></i>
                    <span>
                       {{ translate('please_make_sure_the_provided_links_are_correct_otherwise_buttons_are_redirect_to_wrong_direction') }}.
                    </span>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row gy-3 mb-4">
                            <div class="col-lg-6">
                                <div class="d-flex gap-2 align-items-center justify-content-between mb-3 mb-sm-20">
                                    <div class="d-flex gap-2 align-items-center">
                                        <img width="24"
                                             src="{{dynamicAsset(path: 'public/assets/new/back-end/img/play_store.png') }}"
                                             alt="">
                                        <h3>{{ translate('For_android') }}</h3>
                                    </div>
                                    <label class="switcher">
                                        <input type="checkbox" name="download_google_app_status" value="1"
                                               class="switcher_input" {{isset($downloadVendorApp?->download_google_app_status) && $downloadVendorApp?->download_google_app_status == 1  ? 'checked' : ''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>

                                <div class="p-12 p-sm-20 bg-section rounded">
                                    <div class="form-group">
                                        <label class="form-label">
                                            {{ translate('download_link') }}
                                            <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                  aria-label="{{ translate('if_enabled_the_Google_Play_Store_will_be_visible_in_the_website_footer_section') }}"
                                                  data-bs-title="{{ translate('if_enabled_the_Google_Play_Store_will_be_visible_in_the_website_footer_section') }}">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </label>
                                        <input type="url" name="download_google_app" class="form-control"
                                               value="{{ $downloadVendorApp?->download_google_app}}"
                                               placeholder="{{ translate('Ex: https://play.google.com/store/apps') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-flex gap-2 align-items-center justify-content-between mb-3 mb-sm-20">
                                    <div class="d-flex gap-2 align-items-center">
                                        <img width="24"
                                             src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/apple.png') }}"
                                             alt="">
                                        <h3>{{ translate('For_iOS') }}</h3>
                                    </div>
                                    <label class="switcher">
                                        <input type="checkbox" name="download_apple_app_status" value="1"
                                               class="switcher_input" {{ isset($downloadVendorApp?->download_apple_app_status) && $downloadVendorApp?->download_apple_app_status == 1  ? 'checked' : ''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>

                                <div class="p-12 p-sm-20 bg-section rounded">
                                    <div class="form-group">
                                        <label class="form-label">
                                            {{ translate('download_link') }}
                                            <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                  aria-label="{{ translate('if_enabled_the_download_button_from_the_App_Store_will_be_visible_in_the_Footer_section') }}"
                                                  data-bs-title="{{ translate('if_enabled_the_download_button_from_the_App_Store_will_be_visible_in_the_Footer_section') }}">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                        </label>
                                        <input type="url" name="download_apple_app" class="form-control"
                                               value="{{ $downloadVendorApp?->download_apple_app }}"
                                               placeholder="{{ translate('ex').':'.'https://www.apple.com/app-store/'}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-end gap-3">
                            <button type="reset" class="btn btn-secondary px-4 w-120">
                                {{ translate('reset') }}
                            </button>
                            <button type="submit" class="btn btn-primary px-4 w-120">
                                {{ translate('submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include("layouts.admin.partials.offcanvas._vendor-reg-download-app")
@endsection
