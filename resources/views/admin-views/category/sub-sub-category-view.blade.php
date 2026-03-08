@extends('layouts.admin.app')

@section('title', translate('sub_Sub_Category'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('sub_Sub_Category_Setup') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.sub-sub-category.store') }}" method="POST">
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
                                    <div
                                        class="col-12 tab-content"
                                        id="pills-tabContent">
                                            @foreach($languages as $lang)
                                            <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active' : '' }}" id="{{ $lang }}-form" aria-labelledby="{{ $lang }}-link" role="tabpanel">
                                                <label class="form-label"
                                                    for="exampleFormControlInput1">{{ translate('sub_sub_category_name') }}
                                                    <span class="text-danger">*</span>
                                                    ({{strtoupper($lang) }})</label>
                                                <input type="text" name="name[]" class="form-control"
                                                    placeholder="{{ translate('new_Sub_Sub_Category') }}" {{ $lang == $defaultLanguage? 'required':''}}>
                                            </div>
                                            @endforeach
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    </div>
                                    <input name="position" value="2" class="d-none">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label
                                                class="form-label">{{ translate('main_Category') }}
                                                <span class="text-danger">*</span></label>
                                            <div class="select-wrapper">
                                                <select class="form-select action-get-sub-category-onchange"
                                                    id="main-category" required data-route="{{ route('admin.sub-sub-category.getSubCategory') }}">
                                                <option value="" disabled
                                                        selected>{{ translate('select_main_category') }}</option>
                                                @foreach($parentCategories as $category)
                                                    <option
                                                        value="{{ $category['id']}}">{{ $category['defaultName']}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label text-capitalize" for="name">
                                                {{ translate('sub_category_Name') }}<span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="select-wrapper">
                                                <select name="parent_id" id="parent_id" class="form-select">
                                                    <option value="" disabled selected>
                                                        {{ translate('select_sub_category_first') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label text-capitalize" for="priority">
                                                {{ translate('priority') }}
                                                <span>
                                                    <i class="tio-info-outined" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ translate('the_lowest_number_will_get_the_highest_priority') }}"></i>
                                                </span>
                                            </label>
                                            <div class="select-wrapper">
                                                <select class="form-select" name="priority" id="" required>
                                                    <option disabled selected>{{ translate('set_Priority') }}</option>
                                                    @for ($increment = 0; $increment <= 10; $increment++)
                                                        <option value="{{ $increment }}">{{ $increment }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            <button type="reset"
                                                    class="btn btn-secondary">
                                                {{ translate('reset') }}
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                {{ translate('submit') }}
                                            </button>
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
                                {{ translate('sub_sub_category_list') }}
                                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $categories->total() }}</span>
                            </h3>
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group flex-grow-1 max-w-280">
                                        <input id="" type="search" name="searchValue" class="form-control"
                                               placeholder="{{ translate('search_by_sub_sub_category_name') }}"
                                               value="{{ request('searchValue') }}">
                                        <div class="input-group-append search-submit">
                                            <button type="submit">
                                                <i class="fi fi-rr-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="dropdown">
                                    <a type="button" class="btn btn-outline-primary text-nowrap" href="{{ route('admin.sub-sub-category.export',['searchValue'=>request('searchValue')]) }}">
                                        <img width="14" src="{{dynamicAsset(path: 'public/assets/back-end/img/excel.png')}}" class="excel" alt="">
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
                                        <th>{{ translate('sub_sub_category_name') }}</th>
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
                                            <td>{{ $category['defaultName']}}</td>
                                            <td>{{$category?->parent?->defaultname ?? translate('sub_category_not_found') }}</td>
                                            <td>{{$category?->parent?->parent?->defaultname ??translate('sub_category_not_found') }}</td>
                                            <td class="text-center">{{ $category['priority']}}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-outline-info icon-btn"
                                                    title="{{ translate('edit') }}"
                                                    href="{{ route('admin.sub-sub-category.update',[$category['id']]) }}">
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
                        @if(count($categories) == 0)
                            @include('layouts.admin.partials._empty-state',['text'=>'no_sub_sub_category_found'],['image'=>'default'])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span id="route-admin-category-delete" data-url="{{ route('admin.sub-sub-category.delete') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/products-management.js') }}"></script>
@endpush
