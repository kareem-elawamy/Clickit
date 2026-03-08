$(document).ready(function() {
    $(".custom-select").each(function() {
        let $select = $(this);
        let isInsideOffcanvas = $select.closest(".offcanvas").length > 0; // Check if inside Offcanvas
        let isInsideModal = $select.closest(".modal").length > 0;
        let enableTags = $select.hasClass("tags");
        let isColorSelect = $select.hasClass("color-var-select");
        let isImageSelect = $select.hasClass("image-var-select");

        $select.select2({
            placeholder: $select.data("placeholder"),
            width: "100%",
            allowClear: true,
            minimumResultsForSearch: $select.data("without-search") || 0,
            tags: enableTags,
            maximumSelectionLength:
                $select.data("max-length") !== undefined
                    ? $select.data("max-length")
                    : 0,
            dropdownParent: isInsideOffcanvas
                ? $select.closest(".offcanvas")
                : isInsideModal
                ? $select.closest(".modal")
                : null,
            templateResult: isColorSelect
                ? formatColor
                : isImageSelect
                ? formatImage
                : undefined,
            templateSelection: isColorSelect
                ? formatColor
                : isImageSelect
                ? formatImage
                : undefined
        });

        function formatColor(option) {
            if (!option.id) return option.text;

            let colorCode = $(option.element).data("color");
            if (!colorCode) return option.text;

            return $(
                `<div style="display: flex; align-items: center; gap: 5px;">
                <span style="width: 12px; height: 12px; background-color: ${colorCode}; display: inline-block; border-radius: 3px; margin-right: 8px;"></span>
                ${option.text}
            </div>`
            );
        }

        function formatImage(option) {
            if (!option.id) return option.text;

            let imageUrl = $(option.element).data("image-url");
            if (!imageUrl) return option.text;

            return $(
                `<div style="display: flex; align-items: center; gap: 5px;">
                    <img src="${imageUrl}" alt="${option.text}" style="width: 14px; height: 14px; object-fit: contain;">
                    ${option.text}
                </div>`
            );
        }

        if ($select.prop("multiple")) {
            let $selection = $select
                .next(".select2-container")
                .find(".select2-selection");

            if ($selection.find(".select2-selection__arrow").length === 0) {
                $selection.append(
                    '<span class="select2-selection__arrow"><b role="presentation"></b></span>'
                );
            }

            let updateMoreTag = () => {
                $selection.find(".more").remove();

                let $rendered = $selection.find(".select2-selection__rendered");
                let $choices = $rendered.find(".select2-selection__choice");
                let totalChoices = $choices.length;

                if (totalChoices === 0) return;

                // let totalWidth = $selection.outerWidth() - 100;
                let totalWidth = Math.max($selection.outerWidth() - 100, 0);

                let currentWidth = 0;
                let hiddenCount = 0;

                $choices.each(function() {
                    currentWidth += $(this).outerWidth(true);

                    if (currentWidth >= totalWidth) {
                        hiddenCount++;
                        $(this).hide();
                    }
                });

                if (hiddenCount > 0) {
                    $rendered.append(
                        '<li class="more">+' + hiddenCount + "</li>"
                    );
                }
            };

            updateMoreTag();

            $select.on("change", function() {
                setTimeout(updateMoreTag, 0);
            });
            $select.on("select2:select", function() {
                setTimeout(updateMoreTag, 0);
            });
            $(window).on("resize", function() {
                setTimeout(updateMoreTag, 0);
            });

            $select.on("select2:open", updateMoreTag);
        }
    });
});
