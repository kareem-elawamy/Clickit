@extends('layouts.admin.app')

@section('title', translate('Addon_Activation_Process'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 mb-sm-20">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                {{ translate('Addon_Activation_Process') }}
            </h2>
        </div>

        @if(isset($addonData['deliveryman_app']))
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.system-setup.addon-activation.activation') }}" method="post">
                    @csrf
                    <input type="hidden" name="addon_name" value="deliveryman_app">
                    <input type="hidden" name="software_type" value="addon">
                    <input type="hidden" name="software_id" value="MzYwODUwNDE=">
                    <div class="view-details-container">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <h3>
                                    {{ translate('Deliveryman_App') }}
                                </h3>
                                <p class="mb-1 fs-12">
                                    {{ translate("After_configuring_the_app_enable_the_status_and_set_up_the_required_information_to_activate") }}
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="javascript:"
                                   class="fs-12 fw-semibold d-flex align-items-end view-btn">{{ translate('View') }}
                                    <i class="fi fi-rr-arrow-small-down fs-16 trans3"></i></a>
                                <label class="switcher">
                                    <input
                                        class="switcher_input custom-modal-plugin"
                                        type="checkbox" value="1" name="status"
                                        {{ isset($addonData['deliveryman_app']['activation_status']) && $addonData['deliveryman_app']['activation_status'] == 1 ? 'checked' : '' }}
                                        data-modal-type="input-change-form"
                                        data-modal-form="#smtp-mail-config-form"
                                        data-on-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/maintenance_mode-on.png') }}"
                                        data-off-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/maintenance_mode-off.png') }}"
                                        data-on-title="{{ translate('want_to_Turn_ON_the_Deliveryman_App_addon').'?' }}"
                                        data-off-title="{{ translate('want_to_Turn_OFF_the_Deliveryman_App_addon').'?' }}"
                                        data-on-message="<p>{{ translate('enabling_mail_configuration_services_will_allow_the_system_to_send_emails').'.'.translate('please_ensure_that_you_have_correctly_configured_the_SMTP_settings_to_avoid_potential_issues_with_email_delivery') }}</p>"
                                        data-off-message="<p>{{ translate('disabling_SMTP_mail_configuration_services_stops_email_sending') }}</p>">
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="view-details mt-3 mt-sm-4">
                            <div class="p-12 p-sm-20 bg-section rounded">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label text-capitalize" for="username">
                                                {{ translate('Codecanyon_User_Name') }}
                                                <span class="tooltip-icon"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-placement="right"
                                                      aria-label="{{ translate('please_use_the_codecanyon_username_exactly_as_it_is,_without_any_spaces..') }} {{ translate('make_sure_to_enter_the_name_correctly.') }}"
                                                      data-bs-title="{{ translate('please_use_the_codecanyon_username_exactly_as_it_is,_without_any_spaces..') }} {{ translate('make_sure_to_enter_the_name_correctly.') }}"
                                                >
                                                    <i class="fi fi-sr-info"></i>
                                                </span>
                                            </label>
                                            <input type="text" value="{{ showDemoModeInputValue(value: $addonData['deliveryman_app']['username']) }}"
                                                   placeholder="{{ translate('ex') }}: {{ 'Miler' }}"
                                                   name="username" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label text-capitalize" for="purchase_key">
                                                {{ translate('Codecanyon_Purchase_Code') }}
                                                <span class="tooltip-icon"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-placement="right"
                                                      aria-label="{{ translate('please_check_your_codecanyon_purchase_code_before_proceeding_with_the_update.') }}"
                                                      data-bs-title="{{ translate('please_check_your_codecanyon_purchase_code_before_proceeding_with_the_update.') }}">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                            </label>
                                            <input type="text" value="{{ showDemoModeInputValue(value: $addonData['deliveryman_app']['purchase_key']) }}"
                                                   placeholder="{{ translate('ex') }}: {{ 'CAWFRWRAAWRCAWRA' }}"
                                                   name="purchase_key" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end flex-wrap gap-3 mt-4">
                                <button type="reset" class="btn btn-secondary w-120 px-4">
                                    {{ translate('reset') }}
                                </button>
                                <button class="btn btn-primary w-120 px-4 {{ getDemoModeFormButton(type: 'class') }}"
                                        type="{{ getDemoModeFormButton(type: 'button') }}">
                                    {{ translate('save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
@endsection
