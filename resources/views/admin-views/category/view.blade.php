@extends('layouts.admin.app')

@section('title', translate('category'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('category_Setup') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="table-responsive w-auto overflow-y-hidden mb-4">
                                <div class="position-relative nav--tab-wrapper">
                                    <ul class="nav nav-pills nav--tab lang_tab" id="pills-tab" role="tablist">
                                        @foreach($languages as $lang)
                                        <li class="nav-item px-0">
                                            <a data-bs-toggle="pill" data-bs-target="#{{ $lang }}-form" role="tab" class="nav-link px-2 {{ $lang == $defaultLanguage ? 'active' : '' }}" id="{{ $lang }}-link">
                                                {{ ucfirst(getLanguageName($lang)) . ' (' . strtoupper($lang) . ')' }}
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
                            <div class="row mb-4">
                                <div class="col-lg-6">
                                    <div class="bg-section rounded-8 p-20 w-100 h-100">
                                        <div class="tab-content" id="pills-tabContent">
                                            @foreach($languages as $lang)
                                            <div class="form-group tab-pane fade {{ $lang == $defaultLanguage ? 'show active' : '' }}" id="{{ $lang }}-form" aria-labelledby="{{ $lang }}-link" role="tabpanel">
                                                <label class="form-label text-capitalize">{{ translate('category_Name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                                <input type="text" name="name[]" class="form-control" placeholder="{{ translate('new_Category') }}" {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                            @endforeach
                                            <input name="position" value="0" class="d-none">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label text-capitalize" for="priority">{{ translate('priority') }}
                                                <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                aria-label="{{ translate('the_lowest_number_will_get_the_highest_priority') }}"
                                                data-bs-title="{{ translate('the_lowest_number_will_get_the_highest_priority') }}"
                                                >
                                                <i class="fi fi-sr-info"></i>
                                                </span>
                                            </label>
                                           <div class="select-wrapper">
                                                <select class="form-select" name="priority" id="" required>
                                                    <option disabled selected>{{ translate('set_Priority') }}</option>
                                                    @for ($i = 0; $i <= 10; $i++)
                                                        <option value="{{ $i}}">{{ $i}}</option>
                                                    @endfor
                                                </select>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-4 mt-lg-0 from_part_2">
                                    <div class="d-flex justify-content-center align-items-center bg-section rounded-8 p-20 w-100 h-100">
                                        <div class="d-flex flex-column gap-30">
                                            <div class="text-center">
                                                <label for="" class="form-label fw-semibold mb-1">
                                                    {{ translate('category_Logo') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <p class="fs-12 mb-0"> {{ translate('Upload_image') }}</p>
                                            </div>
                                            <div class="upload-file">
                                                <input type="file" name="image" id="category-image" class="upload-file__input single_file_input"
                                                    accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"  value="" required>
                                                <label
                                                    class="upload-file__wrapper">
                                                    <div class="upload-file-textbox text-center">
                                                        <img width="34" height="34" class="svg" src="{{dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg')}}" alt="image upload">
                                                        <h6 class="mt-1 fw-medium lh-base text-center">
                                                            <span class="text-info">{{ translate('Click to upload') }}</span>
                                                            <br>
                                                            {{ translate('or drag and drop') }}
                                                        </h6>
                                                    </div>
                                                    <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="">
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
                                            <p class="fs-10 mb-0 text-center">{{ translate('JPG,_JPEG,_PNG_image_size_:_max_2_MB') }} <span class="text-dark fw-medium">{{ THEME_RATIO[theme_root_path()]['Category Image'] }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3 justify-content-end">
                                <button type="reset" id="reset"
                                        class="btn btn-secondary min-w-120">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary min-w-120">{{ translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-20" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-20">
                        <div class="d-flex justify-content-between align-items-center gap-20 flex-wrap">
                            <h3 class="mb-0">
                                {{ translate('category_list') }}
                                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $categories->total() }}</span>
                            </h3>
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group flex-grow-1 max-w-280">
                                        <input id="" type="search" name="searchValue" class="form-control"
                                                   placeholder="{{ translate('search_by_category_name') }}"
                                                   value="{{ request('searchValue') }}">
                                        <div class="input-group-append search-submit">
                                            <button type="submit">
                                                <i class="fi fi-rr-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="dropdown">
                                    <a type="button" class="btn btn-outline-primary text-nowrap" href="{{ route('admin.category.export',['searchValue'=>request('searchValue')]) }}">
                                        <img width="14" src="{{dynamicAsset(path: 'public/assets/new/back-end/img/excel.png')}}" class="excel" alt="">
                                        <span class="ps-2">{{ translate('export') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead class="text-capitalize">
                                    <tr>
                                        <th>{{ translate('ID') }}</th>
                                        <th class="text-center">{{ translate('category_Image') }}</th>
                                        <th>{{ translate('name') }}</th>
                                        <th class="text-center">{{ translate('priority') }}</th>
                                        <th class="text-center">{{ translate('home_category_status') }}</th>
                                        <th class="text-center">{{ translate('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $key=>$category)
                                        <tr>
                                            <td>{{ $category['id'] }}</td>
                                            <td class="d-flex justify-content-center">
                                                <div class="avatar-60 d-flex align-items-center rounded-circle overflow-hidden">
                                                    <img class="w-100 h-100 object-fit-cover" alt=""
                                                        src="{{ getStorageImages(path: $category->icon_full_url, type: 'backend-category') }}">
                                                </div>
                                            </td>
                                            <td>{{ $category['defaultname'] }}</td>
                                            <td class="text-center">
                                                {{ $category['priority'] }}
                                            </td>
                                            <td class="text-center">

                                                <form action="{{ route('admin.category.status') }}" method="post"
                                                    id="category-status{{ $category['id'] }}-form" class="no-reload-form">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $category['id'] }}">
                                                    <label class="switcher mx-auto" for="category-status{{ $category['id'] }}">
                                                        <input
                                                            class="switcher_input custom-modal-plugin"
                                                            type="checkbox" value="1" name="home_status"
                                                            id="category-status{{ $category['id'] }}"
                                                            {{ $category['home_status'] == 1 ? 'checked' : '' }}
                                                            data-modal-type="input-change-form"
                                                            data-modal-form="#category-status{{ $category['id'] }}-form"
                                                            data-on-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/category-status-on.png') }}"
                                                            data-off-image="{{ dynamicAsset(path: 'public/assets/new/back-end/img/modal/category-status-off.png') }}"
                                                            data-on-title="{{ translate('Want_to_Turn_ON').' '.$category['defaultname'].' '. translate('status') }}"
                                                            data-off-title="{{ translate('Want_to_Turn_OFF').' '.$category['defaultname'].' '.translate('status') }}"
                                                            data-on-message="<p>{{ translate('if_enabled_this_category_it_will_be_visible_from_the_category_wise_product_section_in_the_website_and_customer_app_in_the_homepage') }}</p>"
                                                            data-off-message="<p>{{ translate('if_disabled_this_category_it_will_be_hidden_from_the_category_wise_product_section_in_the_website_and_customer_app_in_the_homepage') }}</p>"
                                                            data-on-button-text="{{ translate('turn_on') }}"
                                                            data-off-button-text="{{ translate('turn_off') }}">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-3">
                                                    <a class="btn btn-outline-info icon-btn edit" title="Edit" href="{{ route('admin.category.update',[$category['id']]) }}">
                                                        <i class="fi fi-sr-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-outline-danger icon-btn delete-category"
                                                    title="{{ translate('delete') }}"
                                                    data-product-count = "{{count($category?->product)}}"
                                                    data-text="{{translate('there_were_').count($category?->product).translate('_products_under_this_category').'.'.translate('please_update_their_category_from_the_below_list_before_deleting_this_one').'.'}}"
                                                    id="{{ $category['id'] }}">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4">
                            <div class="d-flex justify-content-lg-end">
                                {{ $categories->links() }}
                            </div>
                        </div>
                        @if(count($categories) == 0)
                            @include('layouts.admin.partials._empty-state',['text'=>'no_category_found'],['image'=>'default'])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="route-admin-category-delete" data-url="{{ route('admin.category.delete') }}"></span>
    <span id="get-categories" data-categories="{{ json_encode($categories) }}"></span>
    <div class="modal fade" id="select-category-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                    <button type="button" class="btn-close border-0 btn-circle bg-section2 shadow-none" data-bs-dismiss="modal" aria-label="Close"><i
                            class="tio-clear"></i></button>
                </div>
                <div class="modal-body px-4 px-sm-5 pt-0 pb-sm-5">
                    <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
                        <div
                            class="toggle-modal-img-box d-flex flex-column justify-content-center align-items-center mb-3 position-relative">
                            <img src="{{dynamicAsset('public/assets/new/back-end/img/icons/info.svg')}}" alt="" width="90"/>
                        </div>
                        <h5 class="modal-title mb-2 category-title-message category-title-message"></h5>
                    </div>
                    <form action="{{ route('admin.category.delete') }}" method="post" class="product-category-update-form-submit">
                        @csrf
                        <input name="id" hidden="">
                        <div class="d-flex flex-column gap-2 mb-3">
                            <label class="title-color"
                                   for="exampleFormControlSelect1">{{ translate('select_Category') }}
                                <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" class="form-control js-select2-custom category-option" required>
                            </select>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn-primary min-w-120">{{translate('update')}}</button>
                            <button type="button" class="btn btn-danger min-w-120" data-bs-dismiss="modal">
                                {{ translate('cancel') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/products-management.js') }}"></script>
@endpush
