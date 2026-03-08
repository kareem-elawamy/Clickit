@extends('layouts.admin.app')

@section('title',translate('add_new_delivery_man'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/add-new-delivery-man.png')}}" alt="">
                {{translate('add_new_delivery_man')}}
            </h2>
        </div>

        <div class="row">
            <div class="col-12">
                <form action="{{route('admin.delivery-man.add')}}" method="post" enctype="multipart/form-data" id="add-delivery-man-form">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-0 page-header-title d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                                <i class="fi fi-sr-user"></i>
                                {{translate('general_Information')}}
                            </h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex" for="f_name">{{translate('first_Name')}}</label>
                                        <input type="text" name="f_name" value="{{old('f_name')}}" class="form-control" placeholder="{{translate('first_Name')}}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex" for="exampleFormControlInput1">{{translate('last_Name')}}</label>
                                        <input value="{{old('l_name')}}"  type="text" name="l_name" class="form-control" placeholder="{{translate('last_Name')}}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex" for="exampleFormControlInput1">{{translate('phone')}}</label>
                                        <div class="input-group mb-3">
                                            <div>
                                                <select class="js-example-basic-multiple js-states js-example-responsive form-select max-w-140"
                                                    name="country_code">
                                                    @foreach ($telephoneCodes as $code)
                                                        <option value="{{ $code['code'] }}" {{old($code['code']) == $code['code']? 'selected' : ''}}>{{ $code['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input value="{{old('phone')}}" type="text" name="phone" class="form-control" placeholder="{{translate('ex').':'.'017********'}}">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex" for="exampleFormControlInput1">{{translate('identity_Type')}}</label>
                                        <select name="identity_type" class="form-control">
                                            <option value="passport">{{translate('passport')}}</option>
                                            <option value="driving_license">{{translate('driving_License')}}</option>
                                            <option value="nid">{{translate('nid')}}</option>
                                            <option value="company_id">{{translate('company_ID')}}</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex" for="exampleFormControlInput1">{{translate('identity_Number')}}</label>
                                        <input value="{{ old('identity_number') }}"  type="text" name="identity_number" class="form-control"
                                               placeholder="{{translate('ex').':'.'DH-23434-LS'}}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex" for="exampleFormControlInput1">{{translate('address')}}</label>
                                        <div class="input-group mb-3">
                                            <textarea name="address" class="form-control" id="address" rows="1" placeholder="{{translate('address')}}">{{ old('address') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="mb-2">{{translate('deliveryman_image')}}</label>
                                        <span class="text-info">* ( {{translate('ratio')}} 1:1 )</span>
                                        <div class="custom-file">
                                            <input value="{{ old('image') }}" type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff,.webp|image/*">
                                            <label class="custom-file-label" for="customFileEg1">{{translate('choose_File')}}</label>
                                        </div>
                                        <div class="mt-4 text-center">
                                            <img class="upload-img-view" id="viewer"
                                                 src="{{dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg')}}" alt="{{translate('delivery_man_image')}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="mb-2" for="exampleFormControlInput1">{{translate('identity_image')}}</label>
                                        <div>
                                            <div class="row" id="coba"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h3 class="mb-0 page-header-title d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                                <i class="fi fi-sr-user"></i>
                                {{translate('account_Information')}}
                            </h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex" for="exampleFormControlInput1">{{translate('email')}}</label>
                                        <input value="{{old('email')}}" type="email" name="email" class="form-control" placeholder="{{translate('ex').':'.'ex@example.com'}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex align-items-center gap-2" for="user_password">
                                            {{translate('password')}}
                                            <span class="input-label-secondary cursor-pointer d-flex" data-bs-toggle="tooltip" data-bs-title="{{translate('The_password_must_be_at_least_8_characters_long_and_contain_at_least_one_uppercase_letter').','.translate('_one_lowercase_letter').','.translate('_one_digit_').','.translate('_one_special_character').','.translate('_and_no_spaces').'.'}}">
                                                <i class="fi fi-rr-info"></i>
                                            </span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="js-toggle-password form-control password-check" name="password" id="user_password" placeholder="{{ translate('password_minimum_8_characters') }}">
                                            <div id="changePassTarget" class="input-group-append changePassTarget">
                                                <a class="text-body-light" href="javascript:">
                                                    <i id="changePassIcon" class="fi fi-sr-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label class="mb-2 d-flex text-capitalize" for="confirm_password">
                                            {{translate('confirm_password')}}
                                        </label>

                                        <div class="input-group">
                                            <input type="password" class="js-toggle-password form-control" name="confirm_password" id="confirm_password" placeholder="{{ translate('password_minimum_8_characters') }}">
                                            <div id="changeConfirmPassTarget" class="input-group-append changePassTarget">
                                                <a class="text-body-light" href="javascript:">
                                                    <i id="changeConfirmPassIcon" class="fi fi-sr-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="reset" id="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                <button type="button" class="btn btn-primary form-submit" data-form-id="add-delivery-man-form" data-redirect-route="{{route('admin.delivery-man.list')}}"
                                        data-message="{{translate('want_to_add_this_delivery_man').'?'}}">{{translate('submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <span id="coba-image" data-url="{{dynamicAsset(path: "public/assets/back-end/img/400x400/img2.jpg")}}"></span>
    <span id="extension-error" data-text="{{ translate("please_only_input_png_or_jpg_type_file") }}"></span>
    <span id="size-error" data-text="{{ translate("file_size_too_big") }}"></span>
@endsection

@push('script')
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js')}}"></script>
    <script src="{{dynamicAsset(path: 'public/assets/backend/admin/js/user-management/deliveryman.js')}}"></script>
@endpush
