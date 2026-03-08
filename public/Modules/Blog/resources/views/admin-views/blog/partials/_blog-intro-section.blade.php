<div class="card mb-3">
    <form action="{{ route('admin.blog.intro') }}" method="post" id="blog-custom-status-form" data-id="blog-custom-status-form">
        @csrf
        <div class="card-body">
            <h3 class="mb-4">{{ translate('Intro_Section') }}</h3>
            <div class="position-relative nav--tab-wrapper">
                <ul class="nav nav-pills nav--tab lang_tab gap-3 mb-4">
                    @foreach($languages as $lang)
                        <li class="nav-item text-capitalize px-0 {{$lang == $defaultLanguage ? 'active':''}}">
                            <a class="nav-link lang-link form-system-language-tab px-2 {{$lang == $defaultLanguage ? 'active':''}}"
                            href="javascript:"
                            id="{{$lang}}-link">{{getLanguageName($lang).'('.strtoupper($lang).')'}}</a>
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
                @php
                    $titleData = getWebConfig(name: 'blog_feature_title') ?? [];
                    $subTitleData = getWebConfig(name: 'blog_feature_sub_title') ?? [];
                @endphp

                @foreach($languages as $lang)
                    <div class="{{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                         id="{{$lang}}-form">
                        <div class="form-group">
                            <label class="form-label" for="en_name">
                                {{ translate('title') }}({{strtoupper($lang)}})
                                <span class="input-required-icon">{{ $lang == 'en' ? '*' : '' }}</span>
                            </label>
                            <input type="text" value="{{ $titleData[$lang] ?? '' }}" name="title[{{$lang}}]"
                                   id="" class="form-control"
                                   placeholder="{{ translate('Enter_title') }}" {{$lang == $defaultLanguage ? 'required':''}}>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="">
                                {{ translate('sub_title') }}({{strtoupper($lang)}})
                            </label>
                            <textarea name="sub_title[{{$lang}}]" id="" class="form-control h-90px"
                                      placeholder="{{ translate('Enter_sub_title') }}">{{ $subTitleData[$lang] ?? '' }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-end gap-3 mt-4">
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-secondary">
                        {{ translate('reset') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        @if($titleData || $subTitleData)
                            {{ translate('update') }}
                        @else
                            {{ translate('save') }}
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
