
function renderProductAjaxSetup() {
    $.ajaxSetup({
        headers: {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
    });
}

function getUpdateDigitalVariationFunctionality() {
    renderProductAjaxSetup();

    $.ajax({
        type: "POST",
        url: $("#route-admin-products-digital-variation-combination").data("url"),
        data: $("#product_form").serialize(),
        success: function (data) {
            $("#digital-product-variation-section").html(data.view);
            ProductVariationFileUploadFunctionality();
            deleteDigitalVariationFileFunctionality();
            reinitializeTooltips();
        },
    });
}

function deleteDigitalVariationFileFunctionality() {
    $(".digital-variation-file-delete-button").on("click", function () {
        let variantKey = $(this).data("variant");
        let productId = $(this).data("product");

        renderProductAjaxSetup();

        $.ajax({
            type: "POST",
            url: $("#route-admin-products-digital-variation-file-delete").data(
                "url"
            ),
            data: {
                product_id: productId,
                variant_key: variantKey,
            },
            success: function (response) {
                getUpdateDigitalVariationFunctionality();
                if (response.status === 1) {
                    toastMagic.success(response.message)
                } else {
                    toastMagic.error(response.message)
                }
            },
        });
    });
}

function getUpdateSKUFunctionality() {
    renderProductAjaxSetup();
    $.ajax({
        type: "POST",
        url: $("#route-admin-products-sku-combination").data("url"),
        data: $("#product_form").serialize(),
        success: function (data) {
            console.log(data);
            $("#sku_combination").html(data.view);
            updateProductQuantity();
            updateProductQuantityByKeyUp();
            let productType = $("#product_type").val();
            if (productType && productType.toString() === "physical") {
                if (data.length > 1) {
                    $("#quantity").hide();
                } else {
                    $("#quantity").show();
                }
            }
            generateSKUPlaceHolder();
            removeSymbol();
        },
    });
}

let productAddUpdateMessages = $('#product-add-update-messages');

function getProductAddRequirementsCheck() {
    Swal.fire({
        title: productAddUpdateMessages?.data('are-you-sure'),
        text: productAddUpdateMessages?.data('want-to-add'),
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonColor: "#377dff",
        confirmButtonText: productAddUpdateMessages?.data('yes-word'),
        cancelButtonText: productAddUpdateMessages?.data('no-word'),
        reverseButtons: true,
    }).then((result) => {
        if (result.value) {
            let discountValue = parseFloat($("#discount").val());
            let submitStatus = 1;

            if (submitStatus === 1) {
                let formData = new FormData(
                    document.getElementById("product_form")
                );

                renderProductAjaxSetup();
                $.ajax({
                    type: "POST",
                    method: "POST",
                    url: $("#product_form").attr("action"),
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $("#loading").fadeIn();
                    },
                    success: function (data) {
                        if (data.errors) {
                            for (let i = 0; i < data.errors.length; i++) {
                                setTimeout(() => {
                                    toastMagic.error(data.errors[i].message);
                                }, i * 500);
                            }
                        } else {
                            toastMagic.success($("#message-product-added-successfully").data("text"));
                            $("#product_form").submit();
                        }
                    },
                    complete: function () {
                        $("#loading").fadeOut();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $("#loading").fadeOut();
                    },
                });
            }
        }
    });
}

$(".delete_preview_file_input").on("click", function () {
    let parentDiv = $(this).parent().parent();
    parentDiv.find('input[type="file"]').val("");
    parentDiv.find(".image-uploader__title").html($(".image-uploader__title").data("default"));
    $(this).removeClass("delete_preview_file_input");

    let formData = new FormData(document.getElementById("product_form"));
    renderProductAjaxSetup();

    $.post({
        url: $(this).data("route"),
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.errors) {
                for (let i = 0; i < response.errors.length; i++) {
                    setTimeout(() => {
                        toastMagic.error(response.errors[i].message);
                    }, i * 500);
                }
            } else {
                toastMagic.success(response.message);
                parentDiv
                    .find(".image-uploader__title")
                    .html($(".image-uploader__title").data("default"));
            }
        },
    });
});
