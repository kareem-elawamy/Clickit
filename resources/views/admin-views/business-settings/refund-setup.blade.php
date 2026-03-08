@extends('layouts.admin.app')

@section('title', translate('Refund_Setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 mb-sm-20">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                {{ translate('refund') }}
            </h2>
        </div>

        @include('admin-views.business-settings.business-setup-inline-menu')

        <form action="{{ route('admin.business-settings.refund-setup') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="card">
                <div class="card-body d-flex flex-column gap-3 gap-sm-20">
                    <div class="p-12 p-sm-20 bg-section rounded">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <h2>{{ translate('Refund_Order') }}</h2>
                                <p class="mb-0">
                                    {{ translate('here_you_can_set_up_how_many_days_a_customer_has_to_send_a_refund_request_and_how_can_admin_send_the_refund_amount_,_to_change_the_customer_wallet_setting_,') }}
                                    <a href="{{ route('admin.business-settings.customer-settings') }}"
                                       target="_blank"
                                       class="text-decoration-underline fw-semibold">
                                        {{ translate('click_here') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-12 p-sm-20 bg-section rounded">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    @php($refundDayLimit = getWebConfig('refund_day_limit'))
                                    <label class="form-label" for="">
                                        {{ translate('Refund_Order_Validity') }}({{ translate('Days') }})
                                        <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="right"
                                              data-bs-title="{{ translate('enabling_the_option_customers_will_be_able_to_add_funds_to_the_wallet_through_the_available_payment_method.') }}">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                    </label>
                                    <input type="text" class="form-control" name="refund_day_limit"
                                           id="refund_day_limit"
                                           placeholder="{{ translate('ex') . ': ' . '10' }}"
                                           value="{{ $refundDayLimit ?? 0 }}">
                                </div>
                            </div>

                            @php($walletAddRefund = getWebConfig(name: 'wallet_add_refund'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="wallet_add_refund">
                                        {{ translate('add_refunds_to_wallet') }}
                                        <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="right"
                                              data-bs-title="{{ translate('allow_the_option_to_add_refund_amount_automatically_to_customer_wallet_when_admin_or_vendor_refunded_the_request.') }}">
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                                    </label>
                                    <label
                                        class="d-flex justify-content-between align-items-center gap-3 border rounded px-3 py-10 bg-white user-select-none">
                                        <span class="fw-medium text-dark">{{ translate('status') }}</span>
                                        <label class="switcher" for="wallet_add_refund">
                                            <input
                                                class="switcher_input custom-modal-plugin"
                                                type="checkbox" value="1" name="wallet_add_refund"
                                                id="wallet_add_refund"
                                                {{ $walletAddRefund ? 'checked' : '' }}
                                                data-modal-type="input-change"
                                                data-on-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/wallet-on.png') }}"
                                                data-off-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/wallet-off.png') }}"
                                                data-on-title="{{ translate('want_to_Turn_ON_Refund_to_Wallet_option') }}?"
                                                data-off-title="{{ translate('Are_you_sure_you_want_to_turn_OFF_Wallet_option') }}?"
                                                data-on-message="<p>{{ translate('if_enabled_Admin_can_return_the_refund_amount_directly_to_the_customers') }}</p>"
                                                data-off-message="<p>{{ translate('If_disabled,refunds_will_not_be_processed_automatically_to_the_customer’s_wallet.') }}</p>">
                                            <span class="switcher_control"></span>
                                        </label>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="bg-info bg-opacity-10 fs-12 px-12 py-10 text-dark rounded d-flex gap-2 align-items-center">
                            <i class="fi fi-sr-bulb text-info fs-16"></i>
                            <span>
                                {{ translate('if_you_want_to_send_refund_amount_manually_turn_of_the_add_refund_amount_to_wallet.') }}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end trans3">
                        <div class="d-flex justify-content-sm-end justify-content-center gap-3 flex-grow-1 flex-grow-sm-0 bg-white action-btn-wrapper trans3">
                            <button type="reset" class="btn btn-secondary px-3 px-sm-4 w-120">
                                {{ translate('reset') }}
                            </button>
                            <button type="submit" class="btn btn-primary px-3 px-sm-4">
                                <i class="fi fi-sr-disk"></i>
                                {{ translate('save_information') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @include("layouts.admin.partials.offcanvas._refund-setup")
@endsection
