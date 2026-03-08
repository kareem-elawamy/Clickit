<div class="card mt-3 rest-part">
    <div class="card-header">
        <div class="d-flex gap-2">
            <i class="fi fi-sr-user"></i>
            <h3 class="mb-0">{{ translate('product_video') }}</h3>
            <span class="tooltip-icon cursor-pointer" data-bs-toggle="tooltip"
                  aria-label="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}."
                  data-bs-title="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}."
            >
                <i class="fi fi-sr-info"></i>
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label mb-0">
                {{ translate('youtube_video_link') }}
            </label>
            <span class="text-info"> ({{ translate('optional_please_provide_embed_link_not_direct_link') }}.)</span>
        </div>
        <input type="text" name="video_url"
               placeholder="{{ translate('ex').': https://www.youtube.com/embed/5R06LRdUCSE' }}"
               class="form-control" required>
    </div>
</div>
