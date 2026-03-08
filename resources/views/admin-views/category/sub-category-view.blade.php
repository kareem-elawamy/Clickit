@extends('layouts.admin.app')

@section('title', translate('sub_Category'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('sub_Category_Setup') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.sub-category.store') }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="table-responsive w-auto overflow-y-hidden mb-4">
                                <div class="position-relative nav--tab-wrapper">
                                    <ul class="nav nav-pills nav--tab lang_tab" id="pills-tab" role="tablist">
                                        @foreach($languages as $lang)
                                        <li class="nav-item px-0">
                                            <a data-bs-toggle="pill" data-bs-target="#{{ $lang }}-form" role="tab" class="nav-link px-2 {{ $lang == $defaultLanguage ? 'active' : '' }}" id="{{ $lang }}-link">
                                                {{ucfirst(getLanguageName($lang)).'('.strtoupper($lang).')'}}
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
                            <div class="row gy-4">
                                <div class="{{ theme_root_path() == 'theme_aster'?'col-lg-6':'col-lg-12' }}">

                                    <div class="{{ theme_root_path() == 'theme_aster'?'w-100 h-100':'row' }}">
                                        <div class="{{ theme_root_path() == 'theme_aster'?'form-group':'col-md-6 col-lg-4' }}">
                                            <div class="tab-content" id="pills-tabContent">
                                                @foreach($languages as $lang)
                                                    <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active' : '' }}" id="{{ $lang }}-form" aria-labelledby="{{ $lang }}-link" role="tabpanel">
                                                        <label class="form-label" for="exampleFormControlInput1">
                                                            {{ translate('sub_category_name') }}
                                                            <span class="text-danger">*</span>
                                                            ({{strtoupper($lang) }})
                                                        </label>
                                                        <input type="text" name="name[]" class="form-control"
                                                            placeholder="{{ translate('new_Sub_Category') }}" {{ $lang == $defaultLanguage? 'required':''}}>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                @endforeach
                                                <input name="position" value="1" class="d-none">
                                            </div>
                                        </div>
                                        <div class="{{ theme_root_path() == 'theme_aster'?'form-group':'col-md-6 col-lg-4' }}">
                                            <label class="form-label"
                                                   for="exampleFormControlSelect1">{{ translate('main_Category') }}
                                                <span class="text-danger">*</span></label>
                                                <div class="select-wrapper">
                                                    <select id="exampleFormControlSelect1" name="parent_id"
                                                        class="form-select" required>
                                                        <option value="" selected disabled>
                                                            {{ translate('select_main_category') }}
                                                        </option>
                                                        @foreach($parentCategories as $category)
                                                            <option value="{{ $category['id']}}">
                                                                {{ $category['defaultname']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                        </div>
                                        <div class="{{ theme_root_path() == 'theme_aster'?'from-group':'col-md-6 col-lg-4' }}">
                                            <label class="form-label" for="priority">{{ translate('priority') }}
                                                <span class="tooltip-icon"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
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
                                @if (theme_root_path() == 'theme_aster')
                                    <div class="col-lg-6 mt-4 mt-lg-0 from_part_2">
                                        <div class="d-flex justify-content-center align-items-center bg-section rounded-8 p-20 w-100 h-100">
                                            <div class="d-flex flex-column gap-30">
                                                <div class="text-center">
                                                    <label for="" class="form-label fw-semibold mb-1">
                                                        {{ translate('sub_category_Logo') }}
                                                    </label>
                                                    <h4 class="mb-0"><span class="text-info-dark"> {{ THEME_RATIO[theme_root_path()]['Category Image'] }}</span></h4>
                                                </div>
                                                <div class="upload-file">
                                                    <input type="file" name="image" id="category-image" class="upload-file__input single_file_input"
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"  value="" required>
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
                                                <p class="fs-12 mb-0 text-center">Image size : Max 2 MB</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                                    </div>
                                </div>
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
                                {{ translate('sub_category_list') }}
                                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $categories->total() }}</span>
                            </h3>
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group flex-grow-1 max-w-280">
                                        <input id="" type="search" name="searchValue" class="form-control"
                                               placeholder="{{ translate('search_by_sub_category_name') }}"
                                               value="{{ request('searchValue') }}">
                                        <div class="input-group-append search-submit">
                                            <button type="submit">
                                                <i class="fi fi-rr-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="dropdown">
                                    <a type="button" class="btn btn-outline-primary text-nowrap" href="{{ route('admin.sub-category.export',['searchValue'=>request('searchValue')]) }}">
                                        <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}" class="excel" alt="">
                                        <span class="ps-2">{{ translate('export') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-nowrap align-middle">
                                <thead class="text-capitalize">
                                <tr>
                                    <th>{{ translate('ID') }}</th>
                                    @if (theme_root_path() == 'theme_aster')
                                        <th class="text-center">{{ translate('sub_category_Image') }}</th>
                                    @endif
                                    <th>{{ translate('sub_category_name') }}</th>
                                    <th>{{ translate('category_name') }}</th>
                                    <th class="text-center">{{ translate('priority') }}</th>
                                    <th class="text-center">{{ translate('action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $key=>$category)
                                    <tr>
                                        <td>{{ $category['id']}}</td>
                                        @if (theme_root_path() == 'theme_aster')
                                            <td class="text-center">
                                                <div class="avatar-60 d-flex align-items-center rounded overflow-hidden">
                                                    <img class="w-100 h-100 object-fit-cover" alt=""
                                                         src="{{ getStorageImages(path: $category->icon_full_url , type: 'backend-basic') }}">
                                                </div>
                                            </td>
                                        @endif
                                        <td>{{ ($category['defaultname']) }}</td>
                                        <td>{{$category?->parent?->defaultname ?? translate('category_not_found') }}</td>
                                        <td class="text-center">{{ $category['priority']}}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info icon-btn"
                                                   title="{{ translate('edit') }}"
                                                   href="{{ route('admin.sub-category.update',[$category['id']]) }}">
                                                   <i class="fi fi-sr-pencil"></i>
                                                </a>
                                                <a class="btn btn-outline-danger icon-btn category-delete-button"
                                                   title="{{ translate('delete') }}"
                                                   id="{{ $category['id']}}">
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

                        @if(count($categories)==0)
                            @include('layouts.admin.partials._empty-state',['text'=>'no_sub_category_found'],['image'=>'default'])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span id="route-admin-category-delete" data-url="{{ route('admin.sub-category.delete') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/products-management.js') }}"></script>
@endpush
