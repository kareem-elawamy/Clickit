@extends('layouts.admin.app')

@section('title', translate('update_delivery_man'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/deliveryman.png') }}" width="20"
                    alt="">
                {{ translate('update_Deliveryman') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <form action="{{ route('admin.delivery-man.update', [$deliveryMan['id']]) }}" method="post"
                    id="update-delivery-man-form" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-0 page-header-title d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                                <i class="fi fi-sr-user"></i>
                                {{ translate('general_Information') }}
                            </h3>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex">{{ translate('first_Name') }}</label>
                                        <input type="text" value="{{ $deliveryMan['f_name'] }}" name="f_name"
                                            class="form-control" placeholder="{{ translate('new_delivery_man') }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex">{{ translate('last_Name') }}</label>
                                        <input type="text" value="{{ $deliveryMan['l_name'] }}" name="l_name"
                                            class="form-control" placeholder="{{ translate('last_Name') }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex"
                                            for="exampleFormControlInput1">{{ translate('phone') }}</label>
                                        <div class="input-group mb-3">
                                            <div>
                                                <select
                                                    class="js-example-basic-multiple js-states js-example-responsive form-sel max-w-140"
                                                    name="country_code" id="colors-selector" required>
                                                    @foreach ($telephoneCodes as $code)
                                                        <option value="{{ $code['code'] }}"
                                                            {{ $code['code'] == $deliveryMan['country_code'] ? 'selected' : '' }}>
                                                            {{ $code['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="text" name="phone" value="{{ $deliveryMan['phone'] }}"
                                                class="form-control"
                                                placeholder="{{ translate('ex') . ':' . '017********' }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex">{{ translate('identity_Type') }}</label>
                                        <select name="identity_type" class="form-control">
                                            <option value="passport"
                                                {{ $deliveryMan['identity_type'] == 'passport' ? 'selected' : '' }}>
                                                {{ translate('passport') }}
                                            </option>
                                            <option value="driving_license"
                                                {{ $deliveryMan['identity_type'] == 'driving_license' ? 'selected' : '' }}>
                                                {{ translate('driving_License') }}
                                            </option>
                                            <option value="nid"
                                                {{ $deliveryMan['identity_type'] == 'nid' ? 'selected' : '' }}>
                                                {{ translate('nid') }}
                                            </option>
                                            <option value="company_id"
                                                {{ $deliveryMan['identity_type'] == 'company_id' ? 'selected' : '' }}>
                                                {{ translate('company_ID') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex">{{ translate('identity_Number') }}</label>
                                        <input type="text" name="identity_number"
                                            value="{{ $deliveryMan['identity_number'] }}" class="form-control"
                                            placeholder="{{ translate('ex') }} : DH-23434-LS" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex">{{ translate('address') }}</label>
                                        <textarea name="address" class="form-control" id="address" rows="1" placeholder="Address">{{ $deliveryMan['address'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-4">
                                        <div class="d-flex mb-2 gap-2 align-items-center">
                                            <label class="mb-2 mb-0">{{ translate('deliveryman_image') }}</label>
                                            <span class="text-info fs-12">* ( {{ translate('ratio') }} 1:1 )</span>
                                        </div>
                                        <div class="upload-file">
                                            <div class="mb-4">
                                                <div class="custom-file">
                                                    <input type="file" name="image" id="customFileEg1"
                                                        class="custom-file-input  upload-file__input"
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                    <label class="custom-file-label"
                                                        for="customFileEg1">{{ translate('choose_File') }}
                                                    </label>
                                                    <div class="overlay">
                                                        <div
                                                            class="d-flex gap-10 justify-content-center align-items-center h-100">
                                                            <button type="button"
                                                                class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="fi fi-sr-eye"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="fi fi-rr-camera"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 text-center">
                                                <img class="upload-img-view" id="viewer"
                                                    src="{{ getStorageImages(path: $deliveryMan->image_full_url, type: 'backend-profile') }}"
                                                    alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex">{{ translate('identity_image') }}</label>
                                        <div>
                                            <div class="row" id="coba">
                                                @if ($deliveryMan['identity_image'])
                                                    @foreach ($deliveryMan->identity_images_full_url as $img)
                                                        <div class="col-md-4 mb-3">
                                                            <img height="150" alt=""
                                                                src="{{ getStorageImages(path: $img, type: 'backend-basic') }}">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h3 class="mb-0 page-header-title d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                                <i class="tio-user"></i>
                                {{ translate('account_Information') }}
                            </h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex">{{ translate('email') }}</label>
                                        <input type="email" value="{{ $deliveryMan['email'] }}" name="email"
                                            class="form-control"
                                            placeholder="{{ translate('ex') . ':' . 'email@example.com' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label for="user_password" class="mb-2 d-flex gap-1 align-items-center">
                                        {{ translate('password') }}
                                        <span class="input-label-secondary cursor-pointer d-flex" data-bs-toggle="tooltip"
                                            data-bs-title="{{ translate('The_password_must_be_at_least_8_characters_long_and_contain_at_least_one_uppercase_letter') . ',' . translate('_one_lowercase_letter') . ',' . translate('_one_digit_') . ',' . translate('_one_special_character') . ',' . translate('_and_no_spaces') . '.' }}">
                                            <i class="fi fi-rr-info"></i>
                                        </span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="js-toggle-password form-control password-check"
                                            name="password" required id="user_password" minlength="8"
                                            placeholder="{{ translate('password_minimum_8_characters') }}">
                                        <div id="changePassTarget" class="input-group-append changePassTarget">
                                            <a class="text-body-light" href="javascript:">
                                                <i id="changePassIcon" class="fi fi-sr-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    {{-- <span class="text-danger mx-1 password-error"></span> --}}
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label for="confirm_password"
                                        class="mb-2 d-flex gap-1 align-items-center">{{ translate('confirm_password') }}</label>
                                    <div class="input-group">
                                        <input type="password" class="js-toggle-password form-control"
                                            name="confirm_password" required id="confirm_password"
                                            placeholder="{{ translate('confirm_password') }}">
                                        <div id="changeConfirmPassTarget" class="input-group-append changePassTarget">
                                            <a class="text-body-light" href="javascript:">
                                                <i id="changeConfirmPassIcon" class="fi fi-sr-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="pass invalid-feedback">{{ translate('repeat_password_not_match') . '.' }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="reset" id="reset"
                                    class="btn btn-secondary">{{ translate('reset') }}</button>
                                <button type="button" class="btn btn-primary form-submit"
                                    data-form-id="update-delivery-man-form"
                                    data-redirect-route="{{ route('admin.delivery-man.list') }}"
                                    data-message="{{ translate('want_to_update_this_delivery_man') . '?' }}">{{ translate('submit') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <span id="coba-image" data-url="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="extension-error" data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="size-error" data-text="{{ translate('file_size_too_big') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/deliveryman.js') }}"></script>
@endpush
