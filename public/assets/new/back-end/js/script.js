(function ($) {
    "use strict";

    // Bootstrap Tooltip Init
    const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );
    const tooltipList = [...tooltipTriggerList].map(
        tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)
    );

    // Document Ready
    $(document).ready(function () {
        // --- sidebar search ---
        $(document).on("keyup", ".search-aside-attribute", function () {
            const value = $(this)
                .val()
                .toLowerCase()
                .trim();

            const $container = $(this).closest(
                ".search-aside-attribute-container"
            );
            const $navItems = $container.find("ul.aside-nav > li");

            let currentTitle = null;
            let menuUnderTitle = [];

            $navItems.each(function () {
                const $el = $(this);
                const text = $el
                    .text()
                    .toLowerCase()
                    .trim();
                const $submenu = $el.find(".aside-submenu");

                if ($el.hasClass("nav-item_title")) {
                    if (currentTitle) {
                        const hasVisible = menuUnderTitle.some($menu =>
                            $menu.is(":visible")
                        );
                        currentTitle.toggle(hasVisible);
                    }

                    currentTitle = $el;
                    menuUnderTitle = [];
                    currentTitle.hide();
                } else {
                    let isVisible = false;

                    if ($submenu.length) {
                        const $subItems = $submenu.find("li");
                        let hasSubMatch = false;

                        $subItems.each(function () {
                            const subText = $(this)
                                .text()
                                .toLowerCase()
                                .trim();
                            if (subText.includes(value)) {
                                hasSubMatch = true;
                            }
                        });

                        const parentMatch = text.includes(value);

                        if (parentMatch || hasSubMatch) {
                            $subItems.show();
                            $submenu.show().addClass("open");
                            $el.show();
                            $el.addClass("sub-menu-opened");
                            isVisible = true;
                        } else {
                            $subItems.hide();
                            $submenu.hide().removeClass("open");
                            $el.hide();
                            $el.removeClass("sub-menu-opened");
                        }
                    } else {
                        const isMatch = text.includes(value);
                        $el.toggle(isMatch);
                        isVisible = isMatch;
                    }

                    if (currentTitle) {
                        menuUnderTitle.push($el);
                    }
                }
            });

            if (currentTitle) {
                const hasVisible = menuUnderTitle.some($menu =>
                    $menu.is(":visible")
                );
                currentTitle.toggle(hasVisible);
            }
        });

        // Aside Mini Toggle
        $(".js-aside-toggle").on("click", function () {
            $("body").toggleClass("aside-mini");
            localStorage.setItem(
                "aside-mini",
                $("body").hasClass("aside-mini")
            );
        });

        /* Parent li add class */
        let body = $("body");
        $(".aside-body")
            .find("ul li")
            .parents(".aside-body ul li")
            .addClass("has-sub-item");

        /* Submenu Opened */
        // $('.aside-submenu').hide();
        $(document).on("click", ".aside-body .has-sub-item > a", function (
            event
        ) {
            event.preventDefault();

            $(this)
                .parent(".has-sub-item")
                .toggleClass("sub-menu-opened");
            if (
                $(this)
                    .siblings("ul")
                    .hasClass("open")
            ) {
                $(this)
                    .siblings("ul")
                    .removeClass("open")
                    .slideUp("10");
            } else {
                $(this)
                    .siblings("ul")
                    .addClass("open")
                    .slideDown("10");
            }
        });

        // Handle submenu positioning on scroll
        const updateSubmenuPositions = () => {
            const windowHeight = $(window).height();
            const MENU_PADDING = 30;

            $(".aside-body .aside-nav > li").each(function () {
                const $menuItem = $(this);
                const $subMenu = $menuItem.find(".aside-submenu");

                if (!$subMenu.length) return;

                let menuItemOffsetTop = $menuItem.offset().top - MENU_PADDING;
                const subMenuHeight = $subMenu.outerHeight();

                // Adjust position if submenu would overflow window
                if (
                    menuItemOffsetTop + subMenuHeight + MENU_PADDING >
                    windowHeight
                ) {
                    const overflow =
                        menuItemOffsetTop +
                        subMenuHeight +
                        MENU_PADDING -
                        windowHeight;
                    menuItemOffsetTop = Math.max(
                        0,
                        menuItemOffsetTop - overflow
                    );
                }

                $subMenu.css("--submenu-dynamic-top", `${menuItemOffsetTop}px`);
            });
        };

        $(".aside .aside-body").on("scroll", updateSubmenuPositions);

        // Initial position calculation
        updateSubmenuPositions();

        // Short Ellipsis Image Name
        function shortenFilename(text, maxLength = 15) {
            const extIndex = text.lastIndexOf(".");
            if (extIndex === -1) return text; // No extension found

            const name = text.substring(0, extIndex);
            const ext = text.substring(extIndex);

            if (name.length > maxLength) {
                return name.substring(0, maxLength) + "... " + ext;
            }

            return text;
        }

        $(".shortname").each(function () {
            const originalText = $(this).text();
            $(this).text(shortenFilename(originalText, 10)); 
        });

        // Image Modal
        $(document).on("click", ".view_btn", function (e) {
           
            e.preventDefault();
            e.stopImmediatePropagation();
            console.log("View button clicked");
            let $card = $(this).closest(".upload-file, .view-img-wrap");
            let $img = $card.find("img.upload-file-img");

            let actualSrc = $img.attr("data-src") || $img.attr("src");

            if (actualSrc) {
                let $modal = $(".imageModal").first();
                let $modalImg = $modal.find("img.imageModal_img");
                let $downloadBtn = $modal.find(".download_btn");

                $modalImg.attr("src", actualSrc);
                $downloadBtn.attr("href", actualSrc);

                $modal.modal("show");
            }
        });

        // Circle Progress Bar
        let $progressPieChart = $(".progress-pie-chart"),
            percent = parseInt($progressPieChart.data("percent")),
            deg = (360 * percent) / 100;
        if (percent > 50) {
            $progressPieChart.addClass("gt-50");
        }
        $(".ppc-progress-fill").css("transform", "rotate(" + deg + "deg)");
        $(".ppc-percents span").html(percent + "%");

        // Read More Button
        let maxLength = 120;

        $(".js-truncate-text").each(function () {
            let fullText = $(this)
                .html()
                .trim();

            if (fullText.length > maxLength) {
                let shortText = fullText.substring(0, maxLength);
                let remainingText = fullText.substring(maxLength);

                $(this).html(`
                    <span class="short-text">${shortText}</span>
                    <span class="dots">...</span>
                    <span class="full-text" style="display: none;">${remainingText}</span>
                    <span class="read-more-btn pointer text-primary text-decoration-underline">Learn More</span>
                `);
            }
        });

        $(document).on("click", ".read-more-btn", function () {
            let parent = $(this).closest(".fs-12");
            let fullTextElement = parent.find(".full-text");
            let dots = parent.find(".dots");

            if (fullTextElement.is(":visible")) {
                fullTextElement.hide();
                dots.show();
                $(this).text("Learn More");
            } else {
                fullTextElement.show();
                dots.hide();
                $(this).text("Show Less");
            }
        });
    });

    /* Active Menu Open */
    $(window).on("load", function () {
        // Offcanvas
        $(".js-offcanvas-body")
            .empty()
            .html(
                $(".aside-body")
                    .clone()
                    .removeClass("py-4")
                    .addClass("p-0")
            );

        // Handle both original aside and dynamically created offcanvas aside
        const initAsideMenu = $asideBody => {
            $asideBody.find("a.active").each(function () {
                $(this)
                    .closest("li.has-sub-item")
                    .addClass("sub-menu-opened")
                    .children(".aside-submenu")
                    .addClass("open")
                    .show();
            });

            // Scroll to active menu
            const $activeLink = $asideBody.find(".aside-nav > li > a.active");
            if ($activeLink.length) {
                $asideBody.animate(
                    {
                        scrollTop:
                            $activeLink.offset().top -
                            $asideBody.offset().top -
                            100
                    },
                    300
                );
            }
        };

        // Initialize main aside
        initAsideMenu($(".aside-body"));

        // Initialize offcanvas aside after it's created
        let hasInitAsideMenuRun = false;

        $(document).on("shown.bs.offcanvas", "#offcanvasAside", function () {
            if (!hasInitAsideMenuRun) {
                initAsideMenu($(".js-offcanvas-body .aside-body"));
                hasInitAsideMenuRun = true;
            }
        });

        // Handle offcanvas aside
        // Remove aside-mini class on smaller screens
        const handleAsideMini = () => {
            if ($(window).width() < 992) {
                $("body").removeClass("aside-mini");
            }
        };

        // Run on load
        handleAsideMini();

        // Run on window resize
        $(window).on("resize", handleAsideMini);

        // var guideModal = new bootstrap.Modal(document.getElementById('guideModal'));
        // guideModal.show();

        $(".apex-legends > div").each(function () {
            let color = $(this).attr("data-color");
            if (color) {
                $(this).css("--data-color", color);
            }
        });
    });

})(jQuery);
