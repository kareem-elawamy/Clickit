"use strict";

function initializePhoneInput(selector, outputSelector) {
    const phoneInput = document.querySelector(selector);
    const outputInput = document.querySelector(outputSelector);
    const phoneNumber = phoneInput.value;
    const countryCodeMatch = phoneNumber.replace(/[^0-9]/g, "");

    const defaultCountryCodeElement = document.querySelector(".system-default-country-code");
    const defaultCountry = defaultCountryCodeElement?.dataset?.value?.toLowerCase() || "bd";

    const initialCountry = countryCodeMatch ? `+${countryCodeMatch}` : defaultCountry;

    let phoneInputInit = window.intlTelInput(phoneInput, {
        initialCountry: initialCountry.toLowerCase(),
        showSelectedDialCode: true,
        useFullscreenPopup: false,
    });

    // Fallback init if no dial code found
    if (!phoneInputInit.selectedCountryData?.dialCode) {
        phoneInputInit.destroy();
        phoneInputInit = window.intlTelInput(phoneInput, {
            initialCountry: defaultCountry,
            showSelectedDialCode: true,
            useFullscreenPopup: false,
        });
    }

    function updateOutputValue() {
        const dialCode = phoneInputInit.selectedCountryData?.dialCode || "";
        const digits = phoneInput.value.replace(/[^0-9]/g, "");
        outputInput.value = `+${dialCode}${digits}`;
    }

    updateOutputValue();

    // When country is changed (by clicking flag dropdown)
    phoneInput.addEventListener("countrychange", updateOutputValue);

    // Click listener for country list
    document.querySelectorAll(".iti__country").forEach(country => {
        country.addEventListener("click", function () {
            const dialCode = this.getAttribute("data-dial-code") || "";
            const digits = phoneInput.value.replace(/[^0-9]/g, "");
            outputInput.value = `+${dialCode}${digits}`;
        });
    });

    // Limit max length (keypress)
    phoneInput.addEventListener("keypress", function (event) {
        if (phoneInput.value.length > 15) {
            event.preventDefault();
        }
    });

    // Add/remove border-danger based on length
    phoneInput.addEventListener("keyup", function () {
        if (phoneInput.value.length < 4) {
            phoneInput.classList.add("border-danger");
        } else {
            phoneInput.classList.remove("border-danger");
        }
    });

    // Input sanitation and update output
    const sanitizeAndUpdate = function (event) {
        if (event.which && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }

        phoneInput.value = phoneInput.value.replace(/[^0-9]/g, "");
        updateOutputValue();
    };

    ["keyup", "keypress", "change"].forEach(evt =>
        phoneInput.addEventListener(evt, sanitizeAndUpdate)
    );
}
