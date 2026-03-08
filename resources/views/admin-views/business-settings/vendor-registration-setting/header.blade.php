@extends('layouts.admin.app')

@section('title', translate('header'))
@section('content')
    <div class="content container-fluid">
        @include('admin-views.business-settings.vendor-registration-setting.partial.inline-menu')
        <form action="{{route('admin.pages-and-media.vendor-registration-settings.index')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gy-3 mb-3 mb-sm-20">
                        <div class="col-md-9">
                            <div>
                                <h2 class="text-capitalize">
                                   {{ translate('header_section') }}
                                </h2>
                                <p class="fs-12 mb-0">
                                   {{ translate('when_you_turn_on_the_status_this_section_will_show_in_vendor_registration_page') }}.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row gy-3 mb-4">
                        <div class="col-lg-8">
                            <div class="p-12 p-sm-20 bg-section rounded h-100">
                                <div class="form-group">
                                    <label class="form-label">
                                        {{translate('title')}}
                                        <span class="text-danger">*</span>
                                        <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="add title" data-bs-title="add title">
                                            <i class="fi fi-sr-info"></i>
                                        </span>
                                    </label>
                                    <textarea class="form-control" name="title" rows="1" placeholder="{{translate('enter_title')}}" data-maxlength="50">{{$vendorRegistrationHeader?->title}}</textarea>
                                    <div class="d-flex justify-content-end">
                                        <span class="text-body-light">0/50</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">
                                        {{translate('sub_title')}}
                                        <span class="text-danger">*</span>
                                        <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="add sub title" data-bs-title="add sub title">
                                            <i class="fi fi-sr-info"></i>
                                        </span>
                                    </label>
                                    <textarea class="form-control text-capitalize" name="sub_title" rows="1" placeholder="{{translate('enter_sub_title')}}" data-maxlength="50">{{$vendorRegistrationHeader?->sub_title}}</textarea>
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
                                        <label for="" class="form-label fw-semibold mb-1 text-capitalize">
                                           {{ translate('Header_Image') }}
                                        </label>
                                        <p class="fs-12 mb-0">{{ translate('Upload_your_Header_Image') }}</p>
                                    </div>
                                    <div class="upload-file">
                                        @php($imagePath = imagePathProcessing(imageData:$vendorRegistrationHeader?->image, path: 'vendor-registration-setting'))
                                        <input type="file" name="image" class="upload-file__input single_file_input"
                                               accept=".webp, .jpg, .jpeg, .png, .gif"  value="{{ getStorageImages(path:$imagePath,type: 'backend-placeholder') ?? '' }}" {{ !empty($imagePath['path']) ? '' : 'required' }}">
                                        <label
                                            class="upload-file__wrapper">
                                            <div class="upload-file-textbox text-center {{ !empty($imagePath['path']) ? 'd-none' : '' }}">
                                                <img width="34" height="34" class="svg" src="{{ getStorageImages(path:$imagePath,type: 'backend-placeholder') ?? '' }}" alt="image upload">
                                                <h6 class="mt-1 fw-medium lh-base text-center">
                                                    <span class="text-info">{{ translate('Click_to_upload') }}</span>
                                                    <br>
                                                    {{ translate('or_drag_and_drop') }}
                                                </h6>
                                            </div>
                                            <img class="upload-file-img" loading="lazy" src="{{ !empty($imagePath['path']) ? getStorageImages(path:$imagePath,type: 'backend-placeholder') ?? '' :  '' }}" data-default-src="{{ getStorageImages(path:imagePathProcessing(imageData: $vendorRegistrationHeader?->image, path: 'vendor-registration-setting'),type: 'backend-banner') ?? '' }}" alt="">
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
                                        {{ translate('jpg,_jpeg,_png,_image_size') }} : {{ translate('Max_2_MB') }} <span class="fw-medium text-dark">{{ "(310 x 240px)" }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary px-4 w-120">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary px-4 w-120">{{translate('save')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include("layouts.admin.partials.offcanvas._vendor-reg-header")
@endsection
@push('script')

@endpush
