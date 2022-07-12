const paymentDetailsContainer = $("#payment_details_cotainer");

$(document).ready(function () {
    $("#address_form").validate();
    var addressForm = $("#address_form");
    var shippingDetailsContainer = $("#shipping_details_container");
    let addressFormInputs = addressForm.find("input");
    addressFormInputs.each(function () {
        let input = $(this);
        let inputId = $(this).attr("id");
        let detailContainer = shippingDetailsContainer.find(`.${inputId}`);
        let oldValue = detailContainer.text();

        if (input.val() !== "") {
            detailContainer.text(input.val());
        }

        $(this).on("input", function () {
            detailContainer.text($(this).val());
            if (detailContainer.text() == "") {
                detailContainer.text(oldValue);
            }
        });
    });

    // coupon input
    let couponCodeForm = $("#coupon_code_form");
    let couponInput = couponCodeForm.find("#coupon_input");
    let removeCouponBtn = couponCodeForm.find("#remove_coupon_btn");
    couponInput.on("input", function () {
        if ($(this).val() == "") {
            removeCouponBtn.removeClass("show");
        } else {
            removeCouponBtn.addClass("show");
        }
    });
    removeCouponBtn.on("click", function () {
        couponInput.val("");
        $(this).removeClass("show");
    })

    body.on("click", "#payment_details_cotainer .apply-coupon-code-btn", function () {
        startPageLoading();
        const applyCouponBtn = $(this);
        const link = applyCouponBtn.data("link");
        const couponCode = applyCouponBtn.closest(".coupon-code-section").find("input[name='coupon-code']").val();

        $.post(link, {couponCode}, function (json) {
            endPageLoading();
            if (!json.error) {
                const couponDiscountAmount = json.couponDiscount;
                const newOrderTotal = json.newOrderTotal;
                const paymentAmountsSection = paymentDetailsContainer.find(".payment-amounts-section");

                paymentAmountsSection.find(".coupon-discount .payment-amount-value").text(`-${couponDiscountAmount}`);
                paymentAmountsSection.find(".grand-total .payment-amount-value").text(newOrderTotal);
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    });
});