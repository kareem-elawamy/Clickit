@extends('layouts.admin.app')

@section('title', translate('attribute'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
                <img src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/attribute.png') }}" alt="">
                {{ translate('attribute_Setup') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.attribute.store') }}" method="post" class="text-start">
                            @csrf

                            <div class="table-responsive w-auto overflow-y-hidden mb-4">
                                <div class="position-relative nav--tab-wrapper">
                                    <ul class="nav nav-pills nav--tab lang_tab text-capitalize" id="pills-tab"
                                        role="tablist">
                                        @foreach($language as $lang)
                                            <li class="nav-item px-0">
                                                <a data-bs-toggle="pill" data-bs-target="#{{ $lang }}-form" role="tab"
                                                   class="nav-link px-2 {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                                   id="{{ $lang }}-link">
                                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
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

                            <div class="tab-content" id="pills-tabContent">
                                @foreach($language as $lang)
                                    <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active' : '' }}"
                                         id="{{ $lang }}-form" aria-labelledby="{{ $lang }}-link" role="tabpanel">
                                        <input type="hidden" id="id">
                                        <label class="form-label" for="name">{{ translate('attribute_Name') }}<span
                                                class="text-danger">*</span>
                                            ({{ strtoupper($lang) }})</label>
                                        <input type="text" name="name[]" class="form-control" id="name"
                                               placeholder="{{ translate('enter_Attribute_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang }}" id="lang">
                                @endforeach
                            </div>

                            <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                                <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-20">
                        <div class="d-flex justify-content-between align-items-center gap-20 flex-wrap">
                            <h3 class="mb-0 d-flex align-items-center gap-2">{{ translate('attribute_list') }}
                                <span
                                    class="badge text-dark bg-body-secondary fw-semibold rounded-50">{{ $attributes->total() }}</span>
                            </h3>
                            <div class="flex-grow-1 max-w-280">
                                <form action="{{ url()->current() }}" method="get">
                                    @csrf
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="searchValue"
                                               class="form-control"
                                               placeholder="{{ translate('search_by_Attribute_Name') }}"
                                               aria-label="Search orders" value="{{ request('searchValue') }}" required>
                                        <div class="input-group-append search-submit">
                                            <button type="submit">
                                                <i class="fi fi-rr-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead class="text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('attribute_Name') }} </th>
                                    <th class="text-center">{{ translate('action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($attributes as $key => $attribute)
                                    <tr>
                                        <td>{{$attributes->firstItem()+$key}}</td>
                                        <td class="text-center">{{ translate($attribute['name'])}}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info icon-btn"
                                                   title="{{ translate('edit') }}"
                                                   href="{{route('admin.attribute.update',[$attribute['id']])}}">
                                                    <i class="fi fi-sr-pencil"></i>
                                                </a>
                                                <a class="btn btn-outline-danger icon-btn attribute-delete-button"
                                                   title="{{ translate('delete') }}"
                                                   id="{{ $attribute['id'] }}">
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
                                {!! $attributes->links() !!}
                            </div>
                        </div>

                        @if(count($attributes) == 0)
                            @include('layouts.admin.partials._empty-state',['text'=>'no_attribute_found'],['image'=>'default'])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span id="route-admin-attribute-delete" data-url="{{ route('admin.attribute.delete') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/products/products-management.js') }}"></script>
@endpush
