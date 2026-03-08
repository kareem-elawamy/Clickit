<div class="card mt-3 rest-part">
    <div class="card-header">
        <div class="d-flex gap-2">
            <i class="fi fi-sr-user"></i>
            <h3 class="mb-0">
                {{ translate('seo_section') }}
                <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                      data-bs-placement="top"
                      title="{{ translate('add_meta_titles_descriptions_and_images_for_products').', '.translate('this_will_help_more_people_to_find_them_on_search_engines_and_see_the_right_details_while_sharing_on_other_social_platforms') }}">
                                <i class="fi fi-sr-info"></i>
                            </span>
            </h3>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">
                        {{ translate('meta_Title') }}
                        <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              title="{{ translate('add_the_products_title_name_taglines_etc_here').' '.translate('this_title_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') .' [ '. translate('character_Limit') }} : 100 ]">
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                    </label>
                    <input type="text" name="meta_title" placeholder="{{ translate('meta_Title') }}"
                           class="form-control" id="meta_title">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        {{ translate('meta_Description') }}
                        <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              title="{{ translate('write_a_short_description_of_the_InHouse_shops_product').' '.translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') .' [ '. translate('character_Limit') }} : 160 ]">
                                        <i class="fi fi-sr-info"></i>
                                    </span>
                    </label>
                    <textarea rows="4" type="text" name="meta_description" id="meta_description"
                              class="form-control"></textarea>
                </div>
            </div>

            <div class="col-md-4">
                <div class="d-flex justify-content-center">
                    <div class="d-flex flex-column gap-20">
                        <div>
                            <label for="meta_Image" class="form-label fw-semibold mb-1">
                                {{ translate('meta_Image') }}
                                <span
                                    class="badge badge-info text-bg-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>
                                <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                                      aria-label="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}."
                                      data-bs-title="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}."
                                >
                                                <i class="fi fi-sr-info"></i>
                                            </span>
                            </label>
                        </div>
                        <div class="upload-file">
                            <input type="file" name="meta_image"
                                   class="upload-file__input single_file_input"
                                   id="meta_image_input"
                                   accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                   value=""
                            >
                            <label
                                class="upload-file__wrapper ratio-2-1">
                                <div class="upload-file-textbox text-center">
                                    <img width="34" height="34"
                                         src="{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}"
                                         alt="image upload">
                                    <h6 class="mt-1 fw-medium lh-base text-center">
                                        <span class="text-info">{{ translate('Click_to_upload') }}</span>
                                        <br>
                                        {{ translate('or_drag_and_drop') }}
                                    </h6>
                                </div>
                                <img class="upload-file-img pre-meta-image-viewer" loading="lazy" src="" data-default-src=""
                                     alt="">
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
                    </div>
                </div>
            </div>
        </div>

        @include('admin-views.product.partials._seo-section')
    </div>
</div>
