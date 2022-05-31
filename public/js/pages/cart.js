const desktopDropDownCart = $("#desktop_cart_dropdown");
$(document).ready(function () {
    // Cart incrementor
    const qtyInput = $("input[name=qty]");
    $(".incrementor-style-1 button.plus").on("click", function () {
        let newVal = increment($(this).closest(".incrementor-style-1").find(qtyInput));
        $(this).closest(".incrementor-style-1").find(qtyInput).val(newVal);
        $(this).closest(".incrementor-style-1").find("button.minus").prop("disabled", false);
    });

    $(".incrementor-style-1 button.minus").on("click", function () {
        let newVal = decrement($(this).closest(".incrementor-style-1").find(qtyInput));
        if (newVal) {
            $(this).closest(".incrementor-style-1").find(qtyInput).val(newVal);
        }
        if (newVal == 1) {
            $(this).prop("disabled", true);
        }
    });


    // CART HANDLING
    $("body").on("click", ".incrementor.incrementor-style-1 button.plus", function () {
        const plusBtn = $(this);
        const incrementorInput = plusBtn.closest(".incrementor-container").find("input[name='qty']")
        const addItemLink = plusBtn.data("link");
        $.post(addItemLink, function (json) {
            if (!json.error) {
                updateDropDownCartQty(json.totalCartQuantity);
                const existingCartItems =  desktopDropDownCart.find(".items-container .item");
                const addedCartItem = json.cartItem;
                existingCartItems.each(function () {
                    const existingItem = $(this);
                    const existingItemId = existingItem.data("item-id");
                    if (existingItemId === addedCartItem.itemId) {
                        existingItem.find(".item-quantity").text(`Quantity: ${addedCartItem.itemQty}`)
                    }
                });
                showAlert("success", json.message);
            } else {
                incrementorInput.val(incrementorInput.val() - 1);
                showAlert("error", json.message);
            }
        });
    });
    // END CART HANDLING
});

function increment(element) {
    return parseInt(element.val()) + 1;
}

function decrement(element) {
    let number = parseInt(element.val());
    if (number > 0) {
        return number - 1;
    }

    return false;
}