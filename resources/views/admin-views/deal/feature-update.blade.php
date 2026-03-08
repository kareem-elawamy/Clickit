@extends('layouts.admin.app')
@section('title', translate('feature_Deal_Update'))
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2 align-items-center">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/featured_deal.png') }}" alt="">
                {{ translate('update_feature_deal') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.deal.update-data',[$deal['id']]) }}"
                              class="text-start onsubmit-disable-action-button"
                              method="post">
                            @csrf
                            @php($language = getWebConfig(name:'pnc_language'))
                            @php($defaultLanguage = 'en')
                            @php($defaultLanguage = $language[0])

                            <div class="position-relative nav--tab-wrapper mb-4">
                                <ul class="nav nav-pills nav--tab lang_tab" id="pills-tab" role="tablist">
                                    @foreach($language as $lang)
                                        <li class="nav-item px-0" role="presentation">
                                            <a class="nav-link px-2 {{ $lang == $defaultLanguage ? 'active':'' }}" id="{{ $lang }}-link" data-bs-toggle="pill" href="#{{ $lang }}-form" role="tab" aria-selected="true">
                                                {{getLanguageName($lang).'('.strtoupper($lang).')' }}
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

                            <div class="form-group">

                                <div class="tab-content" id="pills-tabContent">
                                    @foreach($language as $lang)
                                        <?php
                                        if (count($deal['translations'])) {
                                            $translate = [];
                                            foreach ($deal['translations'] as $t) {
                                                if ($t->locale == $lang && $t->key == "title") {
                                                    $translate[$lang]['title'] = $t->value;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="tab-pane fade {{ $lang == $defaultLanguage ? 'show active':'' }}" id="{{ $lang }}-form" role="tabpanel" aria-labelledby="{{ $lang }}-form-tab">
                                            <input type="text" name="deal_type" value="feature_deal" class="d-none">
                                            <div class="col-md-12">
                                                <label for="name"
                                                    class="form-label">{{ translate('title') }}
                                                    ({{strtoupper($lang) }})</label>
                                                <input type="text" name="title[]" class="form-control" id="title"
                                                    value="{{ $lang==$defaultLanguage?$deal['title']:($translate[$lang]['title']??'') }}"
                                                    placeholder="{{ translate('ex').':'.translate('LUX') }}"
                                                        {{ $lang == $defaultLanguage? 'required':'' }}>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}" id="lang">
                                    @endforeach

                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <label for="name"
                                               class="form-label">{{ translate('start_date') }}</label>
                                        <input type="date" value="{{date('Y-m-d',strtotime($deal['start_date'])) }}"
                                               name="start_date" required id="start-date-time"
                                               class="form-control">
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label for="name"
                                               class="form-label">{{ translate('end_date') }}</label>
                                        <input type="date" value="{{date('Y-m-d', strtotime($deal['end_date'])) }}"
                                               name="end_date" required id="end-date-time"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                        class="btn btn-secondary">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/deal.js') }}"></script>
@endpush
