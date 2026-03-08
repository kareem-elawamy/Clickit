<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/libs/jquery/jquery-3.7.1.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/libs/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/libs/select2/select2.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/select-2-init.js') }}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/backend/libs/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/libs/intl-tel-input/js/utils.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/libs/intl-tel-input/js/intlTelInout-validation.js') }}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/libs/tags-input/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/libs/spartan-multi-image-picker/spartan-multi-image-picker-min.js') }}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/libs/swiper/swiper-bundle.min.js')}}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/backend/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

<script src="{{dynamicAsset(path: 'public/assets/new/back-end/libs/lightbox/lightbox.min.js')}}"></script>

<script src="{{dynamicAsset(path: 'public/assets/new/back-end/libs/moment.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/new/back-end/libs/daterangepicker/daterangepicker.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/new/back-end/libs/daterangepicker/daterangepicker-init.js')}}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/single-image-upload.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/multiple-image-upload.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/file.upload.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/multiple_file_upload.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/multiple-file-upload.js')}}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/product.js') }}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/script.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/script_neha.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/common-script.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/custom.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/custom_old.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/app-utils.js') }}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/common/custom-modal-plugin.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/auto-load-func.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/advance-search/keyword-highlight.js') }}"></script>

{!! ToastMagic::scripts() !!}

@if ($errors->any())
    <script>
        'use strict';
        @foreach($errors->all() as $error)
        toastMagic.error('{{ $error }}');
        @endforeach
    </script>
@endif

@include("layouts.admin.partials._firebase-script")

<script>
    let placeholderImageUrl = "{{ dynamicAsset(path: 'public/assets/new/back-end/img/svg/image-upload.svg') }}";
    const iconPath = "{{ dynamicAsset(path: 'public/assets/new/back-end/img/icons/file.svg') }}";
</script>

@if(App\Utils\Helpers::module_permission_check('order_management') && env('APP_MODE') != 'dev')
    <script>
        'use strict'
        let getInitialDataForPanelTime = parseInt($('#get-initial-data-for-panel-time').data('value'), 10);
        setInterval(function () {
            getInitialDataForPanel();
        }, getInitialDataForPanelTime);
    </script>
@endif

@if(env('APP_MODE') == 'dev')
    <script>
        'use strict'
        function checkDemoResetTime() {
            let currentMinute = new Date().getMinutes();
            if (currentMinute > 55 && currentMinute <= 60) {
                $('#demo-reset-warning').addClass('active');
            } else {
                $('#demo-reset-warning').removeClass('active');
            }
        }
        checkDemoResetTime();
        setInterval(checkDemoResetTime, 60000);
    </script>
@endif
