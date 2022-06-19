$(document).ready(function() {
    $("#address_form").validate();
    // address info
    //@todo: add whole file to frontend
    var addressForm = $("#address_form");
    var shippingDetailsContainer = $("#shipping_details_container");
    let addressFormInputs = addressForm.find("input");
    addressFormInputs.each(function() {
        let inputId = $(this).attr("id");
        let detailContainer = shippingDetailsContainer.find(`.${inputId}`);
        let oldValue = detailContainer.text();
        $(this).on("input", function() {
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
    couponInput.on("input", function() {
        if ($(this).val() == "") {
            removeCouponBtn.removeClass("show");
        } else {
            removeCouponBtn.addClass("show");
        }
    });
    removeCouponBtn.on("click", function() {
        couponInput.val("");
        $(this).removeClass("show");
    })
});