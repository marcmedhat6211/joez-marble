const desktopDropDownCart = $("#desktop_cart_dropdown");
$(document).ready(function () {
    // add to cart
    $("body").on("click", "#wishlist_page .item-container .add-to-cart-btn", function () {
        const addToCartBtn = $(this);
        const addToCartLink = addToCartBtn.data("link");
        addToCartBtn.attr("disabled", true).text("Loading..."); // @todo: translate text

        $.post(addToCartLink, function (json) {
            addToCartBtn.attr("disabled", false).text("Add To Cart"); // @todo: translate text
            if (!json.error) {
                const desktopCartDropdown = $("#desktop_cart_dropdown");
                const itemsContainer = desktopCartDropdown.find(".items-container");
                const items = itemsContainer.find(".item");
                const totalCartQty = json.totalCartQuantity
                const cartItem = json.cartItem;
                itemsContainer.find("p.desktop-cart-empty-txt").remove();
                updateDropDownCartQty(totalCartQty);
                let isItemExist = false;
                if (items.length > 0) {
                    items.each(function () {
                        const existingItem = $(this);
                        const existingItemId = existingItem.data("item-id");
                        if (existingItemId === cartItem.itemId) {
                            isItemExist = true;
                            existingItem.find(".item-quantity").text(`Quantity: ${cartItem.itemQty}`);
                        }
                    })
                }
                if (items.length === 0 || !isItemExist) {
                    drawCartItem(cartItem, itemsContainer);
                    $("img.lazy").lazy({
                        effect: "fadeIn",
                    });
                }
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    });

    //remove from wishlist
    $("body").on("click", "#wishlist_page .item-container .remove-from-wishlist-btn", function () {
        const removeBtn = $(this);
        const removeFromWishlistLink = removeBtn.data("link");
        const itemContainer = removeBtn.closest(".item-main-container");
        removeBtn.attr("disabled", true).text("Loading..."); // @todo: translate text

        $.post(removeFromWishlistLink, function (json) {
            removeBtn.attr("disabled", false).text("Remove"); // @todo: translate text
            if (!json.error) {
                itemContainer.remove();
                if ($("#wishlist_page .item-main-container").length === 0) {
                    addWishlistEmptyMsg();
                }
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    });
});

const addWishlistEmptyMsg = () => {
    const wishlistEmptyMsg = $("<h5>", {
        class: "wishlist-empty-txt text-center my-5",
        text: "Your wishlist is empty" //@todo: translate text
    });
    wishlistEmptyMsg.appendTo("#wishlist_page .container");
}