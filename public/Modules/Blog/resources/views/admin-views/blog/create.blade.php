@extends('layouts.admin.app')

@section('title', translate('Create_New_Blog'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" id="blog-ajax-form">
            @csrf
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <a href="{{ route('admin.blog.view') }}">
                        <i class="fi fi-rr-arrow-left"></i>
                    </a>
                    {{ translate('Create_New_Blog') }}
                </h2>
            </div>

           <div class="card">
                <div class="card-body">
                    <div class="row gy-4 mb-4 align-items-center">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                                    <label for="name" class="form-label mb-0">
                                        {{ translate('Category') }}
                                        <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-title="{{ translate('select_a_category_from_the_dropdown_menu_to_assign_this_blog') }} {{ translate('if_no_categories_are_available_or_want_to_add_a_new_category_please_add_it_from_the_manage_category_section') }}">
                                            <i class="fi fi-sr-info"></i>
                                        </span>
                                    </label>
                                    <a data-bs-toggle="offcanvas" href="#offcanvasCategory" class="user-select-none">
                                        {{ translate('Manage_Category') }}
                                    </a>
                                </div>

                                <select class="custom-select" name="blog_category" id="blog-category-select"
                                        data-text="{{ translate('select') }}"
                                        data-route="{{ route('admin.blog.category.get-list') }}">
                                    <option value="" selected disabled>{{ translate('select') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            @if(getDefaultLanguage() == 'en')
                                                {{ $category->name }}
                                            @else0
                                                {{ $category?->translations()->where('key', 'name')->where('locale', getDefaultLanguage())->first()?->value ?? $category?->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    {{ translate('Writer') }}
                                </label>
                                <input type="text" name="writer" id="" value="{{ old('writer') }}" class="form-control" placeholder="{{ translate('Ex') }}: {{ 'Jhon Milar' }}">
                            </div>
                            <div class="form-group mb-0">
                                <label for="name" class="form-label">
                                    {{ translate('Publish_Date') }}
                                    <span class="tooltip-icon" data-bs-toggle="tooltip" data-bs-title="{{ translate('pick_the_date_that_you_want_to_show_for_customers_as_blog_publishing_date') }}">
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                                </label>
                                <div class="position-relative">
                                    <input type="date" name="publish_date" class="form-control cursor-pointer"
                                           value="{{ date('Y-m-d') }}" placeholder="{{ translate('Select_Date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="d-flex flex-column gap-20">
                                <div class="text-center">
                                    <label for="" class="form-label fw-semibold mb-1">
                                        Thumbnail
                                        <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="upload-file">
                                    <input type="file" name="image" class="upload-file__input single_file_input" accept=".webp, .jpg, .jpeg, .png, .gif" value="" required="">
                                        <button type="button" class="remove_btn btn btn-danger btn-circle w-20 h-20 fs-8" style="opacity: 0;">
                                            <i class="fi fi-sr-cross"></i>
                                        </button>
                                    <label class="upload-file__wrapper w-325">
                                        <div class="upload-file-textbox text-center" style="">
                                            <img width="34" height="34" class="svg" src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}" alt="image upload">
                                            <h6 class="mt-1 fw-medium lh-base text-center">
                                                <span class="text-info">Click to upload</span>
                                                <br>
                                                Or drag and drop
                                            </h6>
                                        </div>
                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
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
                                <p class="fs-10 mb-0 text-center">JPG, JPEG, PNG, Gif Image size : Max 2 MB <span class="fw-medium">(325 x 100 px)</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-section rounded p-4">
                        <div class="position-relative nav--tab-wrapper">
                            <ul class="nav nav-pills nav--tab lang_tab gap-3 mb-4">
                                @foreach($languages as $lang)
                                    <li class="nav-item text-capitalize px-0">
                                        <a class="nav-link lang-link form-system-language-tab px-2 {{$lang == $defaultLanguage ? 'active':''}}" href="javascript:" id="{{$lang}}-link">{{getLanguageName($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
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
                            </ul>
                        </div>
                        <div>
                            @foreach($languages as $lang)
                                <div class="{{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="mb-4">
                                        <label for="name" class="form-label">{{ translate('title')}}
                                            ({{strtoupper($lang)}})
                                            <span class="input-required-icon">*</span>
                                        </label>
                                        <input type="text" name="title[{{$lang}}]" class="form-control" id="title" placeholder="{{translate('ex').':'.translate('LUX')}}">
                                    </div>
                                </div>
                                <input type="hidden" name="lang[{{$lang}}]" value="{{$lang}}" id="lang-{{$lang}}">
                                <div class="form-group mb-0 {{$lang != $defaultLanguage ? 'd-none':''}} form-system-description-language-form" id="{{ $lang}}-description-form">
                                    <label class="form-label">{{ translate('Description') }}({{strtoupper($lang)}}) <span class="input-required-icon">*</span></label>
                                    <div id="description-{{$lang}}-editor" class="quill-editor"></div>
                                    <textarea name="description[{{$lang}}]" id="description-{{$lang}}" style="display:none;"></textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @include('blog::admin-views.blog.partials._seo-section')

                    <input type="hidden" name="status" id="status" value="1">
                    <input type="hidden" name="is_draft" id="is_draft" value="0">

                    <div class="d-flex flex-wrap gap-3 justify-content-end mt-4">
                        <button type="reset" id="reset" class="btn btn-secondary reset-form">
                            {{ translate('reset') }}
                        </button>
                        <a class="btn btn-outline-primary save-draft">
                            {{ translate('Save_to_Draft') }}
                        </a>
                        <button type="button" class="btn btn-primary publish" data-bs-toggle="modal" data-bs-target="#toggle-status-publish-modal">
                            {{ translate('Publish') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @include('blog::admin-views.blog.partials._publish-modal')
    @include('blog::admin-views.blog.category.index')

@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor-init.js') }}"></script>

    @include('blog::admin-views.blog.partials._blog-script')
    @include('blog::admin-views.blog.category.partials._script')
@endpush
