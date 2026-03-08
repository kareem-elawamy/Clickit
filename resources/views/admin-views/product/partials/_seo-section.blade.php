<div class="row g-4 pt-4">
    <div class="col-lg-6 col-xl-5">
        <div class="card border h-100">
            <div class="card-body d-flex flex-wrap gap-2 justify-content-between h-100">
                <div class="item d-flex flex-column gap-2 flex-grow-1">
                    <label class="form-check d-flex gap-2">
                        <input type="radio" name="meta_index" value="index" checked class="form-check-input radio--input">
                        <span class="user-select-none form-check-label">{{ translate('Index') }}</span>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ translate('allow_search_engines_to_put_this_web_page_on_their_list_or_index_and_show_it_on_search_results.') }}">
                            <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                        </span>
                    </label>
    
                    <label class="form-check d-flex gap-2">
                        <input type="checkbox" name="meta_no_follow" value="1" class="input-no-index-sub-element form-check-input checkbox--input">
                        <span class="user-select-none form-check-label">{{ translate('No_Follow') }}</span>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ translate('instruct_search_engines_not_to_follow_links_from_this_web_page.') }}">
                            <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                        </span>
                    </label>
                    <label class="form-check d-flex gap-2">
                        <input type="checkbox" name="meta_no_image_index" value="1" class="input-no-index-sub-element form-check-input checkbox--input">
                        <span class="user-select-none form-check-label">{{ translate('No_Image_Index') }}</span>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ translate('prevents_images_from_being_listed_or_indexed_by_search_engines') }}">
                            <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                        </span>
                    </label>
                </div>
                <div class="item d-flex flex-column gap-2 flex-grow-1">
                    <label class="form-check d-flex gap-2">
                        <input type="radio" name="meta_index" value="noindex" class="action-input-no-index-event form-check-input radio--input">
                        <span class="user-select-none form-check-label">{{ translate('no_index') }}</span>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ translate('disallow_search_engines_to_put_this_web_page_on_their_list_or_index_and_do_not_show_it_on_search_results.') }}">
                            <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                        </span>
                    </label>
                    <label class="form-check d-flex gap-2">
                        <input type="checkbox" name="meta_no_archive" value="1" class="input-no-index-sub-element form-check-input checkbox--input">
                        <span class="user-select-none form-check-label">{{ translate('No_Archive') }}</span>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ translate('instruct_search_engines_not_to_display_this_webpages_cached_or_saved_version.') }}">
                            <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                        </span>
                    </label>
                    <label class="form-check d-flex gap-2">
                        <input type="checkbox" name="meta_no_snippet" value="1" class="input-no-index-sub-element form-check-input checkbox--input">
                        <span class="user-select-none form-check-label">
                            {{ translate('No_Snippet') }}
                        </span>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ translate('instruct_search_engines_not_to_show_a_summary_or_snippet_of_this_webpages_content_in_search_results.') }}">
                            <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-5">
        <div class="card border h-100">
            <div class="card-body d-flex flex-column gap-2 h-100">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="item">
                        <label class="form-check d-flex gap-2">
                            <input type="checkbox" name="meta_max_snippet" value="1" class="form-check-input checkbox--input">
                            <span class="user-select-none form-check-label">
                                {{ translate('max_Snippet') }}
                            </span>
                            <span data-bs-toggle="tooltip" data-bs-title="{{ translate('determine_the_maximum_length_of_a_snippet_or_preview_text_of_the_webpage.') }}">
                                <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                            </span>
                        </label>
                    </div>
                    <div class="item flex-grow-0">
                        <input type="number" placeholder="-1" class="form-control h-30 py-0" name="meta_max_snippet_value" value="-1">
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="item">
                        <label class="form-check d-flex gap-2 m-0">
                            <input type="checkbox" name="meta_max_video_preview" value="1" class="form-check-input checkbox--input">
                            <span class="user-select-none form-check-label">
                                {{ translate('max_Video_Preview') }}
                            </span>
                            <span data-bs-toggle="tooltip" data-bs-title="{{ translate('determine_the_maximum_duration_of_a_video_preview_that_search_engines_will_display') }}">
                                <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                            </span>
                        </label>
                    </div>
                    <div class="item flex-grow-0">
                        <input type="number" placeholder="-1" class="form-control h-30 py-0" name="meta_max_video_preview_value" value="-1">
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="item">
                        <label class="form-check d-flex gap-2 m-0">
                            <input type="checkbox" name="meta_max_image_preview" value="1" class="form-check-input checkbox--input">
                            <span class="user-select-none form-check-label">{{ translate('max_Image_Preview') }}</span>
                            <span data-bs-toggle="tooltip" data-bs-title="{{ translate('determine_the_maximum_size_or_dimensions_of_an_image_preview_that_search_engines_will_display.') }}">
                                <img src="{{ dynamicAsset('public/assets/back-end/img/query.png') }}" alt="">
                            </span>
                        </label>
                    </div>
                    <div class="item w-120 flex-grow-0">
                        <div class="select-wrapper">
                            <select class="form-select h-30 py-0" name="meta_max_image_preview_value">
                                <option value="large">{{ translate('large') }}</option>
                                <option value="medium">{{ translate('medium') }}</option>
                                <option value="small">{{ translate('small') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
