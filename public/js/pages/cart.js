const desktopDropDownCart = $("#desktop_cart_dropdown");
$(document).ready(function () {
    // Cart incrementor
    const qtyInput = $(".incrementor-style-1 input[name=qty]");
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
        if (newVal < 1) {
            $(this).prop("disabled", true);
        }
    });

    // add item to cart
    body.on("click", ".incrementor.incrementor-style-1 button.plus", function () {
        startPageLoading();
        const plusBtn = $(this);
        const incrementorInput = plusBtn.closest(".incrementor-container").find("input[name='qty']")
        const addItemLink = plusBtn.data("link");
        $.post(addItemLink, function (json) {
            endPageLoading();
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
                adjustSummaryBox(json.totalCartQuantity, json.cartTotalPrice, json.cartGrandTotal);
                // MOBILE CART
                $("#mobile_cart .cart-item-container").each(function () {
                    const mobileCartItem = $(this);
                    const mobileCartItemId = mobileCartItem.data("item-id");
                    if (mobileCartItemId == addedCartItem.itemId) {
                        mobileCartItem.find("input[name='qty']").val(addedCartItem.itemQty);
                    }
                });
                showAlert("success", json.message);
            } else {
                incrementorInput.val(incrementorInput.val() - 1);
                $("#mobile_cart .cart-item-container").each(function () {
                    const mobileCartItem = $(this);
                    const mobileCartItemId = mobileCartItem.data("item-id");
                    if (mobileCartItemId == addedCartItem.itemId) {
                        mobileCartItem.find("input[name='qty']").val(mobileCartItem.find("input[name='qty']").val() - 1);
                    }
                });
                showAlert("error", json.message);
            }
        });
    });

    // remove item from cart
    body.on("click", ".incrementor.incrementor-style-1 button.minus", function () {
        startPageLoading();
        const minusBtn = $(this);
        const incrementorInput = minusBtn.closest(".incrementor-container").find("input[name='qty']")
        const removeOneItemUrl = minusBtn.data("link");

        $.post(removeOneItemUrl, function (json) {
            endPageLoading();
            if (!json.error) {
                updateDropDownCartQty(json.cartTotalQty);
                $("#desktop_cart_dropdown").find(".item").each(function() {
                    const item = $(this);
                    if (item.data("item-id") === json.itemId) {
                        if (json.itemQty === 0) {
                            item.remove();
                        } else {
                            item.find(".item-quantity").text(`Quantity: ${json.itemQty}`); //@todo: translate text
                        }
                    }
                });
                adjustSummaryBox(json.cartTotalQty, json.cartTotalPrice, json.cartGrandTotal);
                $("#mobile_cart .cart-item-container").each(function () {
                    const mobileCartItem = $(this);
                    const mobileCartItemId = mobileCartItem.data("item-id");
                    if (mobileCartItemId == json.itemId) {
                        mobileCartItem.find("input[name='qty']").val(json.itemQty);
                    }
                });
                showAlert("success", json.message);
            } else {
                incrementorInput.val(incrementorInput.val() + 1);
                $("#mobile_cart .cart-item-container").each(function () {
                    const mobileCartItem = $(this);
                    const mobileCartItemId = mobileCartItem.data("item-id");
                    if (mobileCartItemId == json.itemId) {
                        mobileCartItem.find("input[name='qty']").val(mobileCartItem.find("input[name='qty']").val() + 1);
                    }
                });
                showAlert("error", json.message);
            }
        });
    });

    // remove whole item from cart
    body.on("click", "#cart_form .cart-item .remove-item-btn", function () {
        const btn = $(this);
        const url = btn.data("link");
        const clickedItem = btn.closest(".cart-item");
        btn.attr("disabled", true).text("loading..."); //@todo: translate text

        $.post(url, function (json) {
            btn.attr("disabled", false).text("Remove"); //@todo: translate text
            if (!json.error) {
                updateDropDownCartQty(json.newCartTotalQuantity);
                btn.closest(".cart-item-container").remove();
                const desktopCartDropDown = $("#desktop_cart_dropdown");
                desktopCartDropDown.find(".item").each(function() {
                    const item = $(this);
                    if (item.data("item-id") === clickedItem.data("item-id")) {
                        item.remove();
                    }
                    if ($("#desktop_cart_dropdown .item").length === 0) {
                        const cartEmptyMsg = $("<p>", {
                            class: "desktop-cart-empty-txt",
                            text: "Your Cart is Empty" //@todo: translate text
                        });
                        cartEmptyMsg.appendTo(desktopCartDropDown.find(".items-container"));
                    }
                });
                adjustSummaryBox(json.newCartTotalQuantity, json.cartTotal, json.cartGrandTotal);
                if ($("#cart_form .cart-item-container").length === 0) {
                    $("#cart_summary").remove();
                    const cartEmptyMsgInPage = $("<h5>", {
                        class: "text-center py-5 mb-0",
                        text: "Your Cart is Empty" //@todo: translate text
                    });
                    cartEmptyMsgInPage.appendTo("#cart_page .container");
                }

                // mobile cart
                $("#mobile_cart .cart-item-container").each(function () {
                    const mobileCartItem = $(this);
                    const mobileCartItemId = mobileCartItem.data("item-id");
                    if (mobileCartItemId == clickedItem.data("item-id")) {
                        mobileCartItem.remove();
                    }
                });
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    });

    // move to wishlist
    body.on("click", "#cart_page .cart-item .move-to-wishlist-btn", function () {
        const moveToWishlistBtn = $(this);
        const moveToWishlistLink = moveToWishlistBtn.data("link");
        const cartItemInPage = moveToWishlistBtn.closest(".cart-item");
        const cartItemInPageId = cartItemInPage.data("item-id");
        moveToWishlistBtn.attr("disabled", true).text("loading..."); // @todo: translate text

        $.post(moveToWishlistLink, function (json) {
           if (!json.error) {
               updateDropDownCartQty(json.newCartTotalQuantity);
               const existingCartItems = $("#desktop_cart_dropdown .item");
               existingCartItems.each(function() {
                   const existingCartItem = $(this);
                   const existingItemId = existingCartItem.data("item-id");
                   if (cartItemInPageId === existingItemId) {
                       existingCartItem.remove();
                   }
               });
               cartItemInPage.closest(".cart-item-container").remove();
               adjustSummaryBox(json.newCartTotalQuantity, json.cartTotal, json.cartGrandTotal);

               if ($("#desktop_cart_dropdown .item").length === 0) {
                   $("#cart_summary").remove();
                   const cartEmptyMsg = $("<p>", {
                       class: "desktop-cart-empty-txt",
                       text: "Your Cart is Empty" //@todo: translate text
                   });
                   cartEmptyMsg.appendTo(desktopDropDownCart.find(".items-container"));
               }

               showAlert("success", json.message);
           } else {
               showAlert("error", json.message);
           }
        });
    });
});

function increment(element) {
    return parseInt(element.val()) + 1;
}

function decrement(element) {
    let number = parseInt(element.val());
    if (number >= 0) {
        return number - 1;
    }

    return false;
}

const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number);
}