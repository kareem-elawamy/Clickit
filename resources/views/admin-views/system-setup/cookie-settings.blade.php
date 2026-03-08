@extends('layouts.admin.app')

@section('title', translate('cookie_settings'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 mb-sm-20">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                {{ translate('system_Setup') }}
            </h2>
        </div>
        @include('admin-views.system-setup.system-settings-inline-menu')

        <form action="{{ route('admin.system-setup.cookie-settings') }}" method="post" enctype="multipart/form-data" id="update-settings">
            @csrf
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center gap-10 mb-3 mb-sm-20">
                        <div>
                            <h2 class="">
                                {{ translate('cookie_settings') }}
                            </h2>
                            <p class="fs-12 mb-0">
                                {{ ('Need Content') }}
                            </p>
                        </div>
                        <label class="switcher" for="cookie-setting-status">
                            <input
                                class="switcher_input custom-modal-plugin"
                                type="checkbox" value="1" name="status"
                                id="cookie-setting-status"
                                {{ isset($cookieSetting) && $cookieSetting['status']==1 ? 'checked' : ''}}
                                data-modal-type="input-change"
                                data-on-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/cookie-on.png') }}"
                                data-off-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/cookie-off.png') }}"
                                data-on-title = "{{ translate('by_Turning_ON_Cookie_Settings') }}"
                                data-off-title = "{{ translate('by_Turning_OFF_Cookie_Settings') }}"
                                data-on-message = "<p>{{ translate('if_you_disable_it_customers_cannot_see_Cookie_Settings_in_frontend') }}</p>"
                                data-off-message = "<p>{{ translate('if_you_enable_it_customers_will_see_Cookie_Settings_in_frontend') }}</p>"
                                data-on-button-text="{{ translate('turn_on') }}"
                                data-off-button-text="{{ translate('turn_off') }}">
                            <span class="switcher_control"></span>
                        </label>
                    </div>
                    <div class="bg-section rounded p-12 p-sm-20 loyalty-point-section" id="cookie_setting_status_section">
                        <div class="form-group">
                            <label class="form-label"
                                for="cookie_text">{{ translate('Cookie_Text') }}
                                <span class="tooltip-icon" data-bs-toggle="tooltip"
                                    data-bs-placement="right" data-bs-html="true"
                                    aria-label="Enter_cookie_text"
                                    data-bs-title="<div class='text-start'>{{ translate('setup_the_content_that_you_want_to_display_to_the_customer_as_cookies_text_in_the_customer_app_and_the_website') }}</div>">
                                    <i class="fi fi-sr-info"></i>
                                </span>
                            </label>
                            <textarea class="form-control" name="cookie_text" rows="2" placeholder="{{ translate('Type_about_the_cookies') }}" data-maxlength="200">{{isset($cookieSetting) ? $cookieSetting['cookie_text'] : ''}}</textarea>
                            <div class="d-flex justify-content-end">
                                <span class="text-body-light"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end trans3">
                <div class="d-flex justify-content-sm-end justify-content-center gap-3 flex-grow-1 flex-grow-sm-0 bg-white action-btn-wrapper trans3">
                    <button type="reset" class="btn btn-secondary px-3 px-sm-4 w-120">{{ translate('reset') }}</button>
                    <button type="{{ getDemoModeFormButton(type: 'button') }}" class="{{ getDemoModeFormButton(type: 'class') }} btn btn-primary px-3 px-sm-4">
                        <i class="fi fi-sr-disk"></i>
                        {{ translate('save_information') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    @include("layouts.admin.partials.offcanvas._cookie-settings")
@endsection
