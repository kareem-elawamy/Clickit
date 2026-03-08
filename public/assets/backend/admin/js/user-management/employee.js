$(document).ready(function () {
    $(function () {
        let coba_image = $('#coba-image').data('url');
        let extension_error = $('#extension-error').data('text');
        let size_error = $('#size-error').data('text');
        $("#coba").spartanMultiImagePicker({
            fieldName: 'identity_image[]',
            maxCount: 5,
            rowHeight: '248px',
            rowWidth: '248px',
            groupClassName: 'col-12 col-md-6',
            maxFileSize: '',
            placeholderImage: {
                image: coba_image,
                width: '100%'
            },
            dropFileLabel: "Drop Here",
            onAddRow: function (index, file) {

            },
            onRenderedPreview: function (index) {

            },
            onRemoveRow: function (index) {

            },
            onExtensionErr: function (index, file) {
                toastMagic.error(extension_error);
            },
            onSizeErr: function (index, file) {
                toastMagic.error(size_error);
            }
        });
    });
});
