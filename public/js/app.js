const body = $("body");
$(document).ready(function () {
    const desktopHeader = $("header #desktop_header");
    const desktopHeaderPartOne = desktopHeader.find("#header_part_one");
    const desktopHeaderPartTwo = desktopHeader.find("#header_part_two");
    const desktopHeaderPartThree = desktopHeader.find("#header_part_three");
    const mobileHeader = $("header #mobile_header");
    const productCard = $(".card.card-style-1");
    const mobileMenuWrapper = $("#mobile_menu_wrapper");
    const mobileMenu = $("#mobile_menu");
    const searchPopup = $("#search_popup");
    let currentSearchRequest = null;
    //lazy loading
    if ($("img.lazy").length > 0) {
        $("img.lazy").lazy({
            effect: "fadeIn",
        });
    }

    // header part two dropdowns
    desktopHeaderPartTwo.find(".icon-container").each(function () {
        let $this = $(this);
        let iconBtn = $this.find(".icon-btn");
        let iconDropdown = $this.find(".icon-dropdown");
        $this.on({
            mouseenter: function () {
                iconDropdown.addClass("show");
                iconBtn.addClass("active");
            },
            mouseleave: function () {
                setTimeout(() => {
                    if ($this.find(".icon-dropdown:hover").length == 0) {
                        iconDropdown.removeClass("show");
                        iconBtn.removeClass("active");
                    }
                }, 100);
            },
        });
    });

    // header part three dropdowns
    desktopHeaderPartThree.find(".header-three-item").each(function () {
        let $this = $(this);
        let itemDropdown = $this.find(".header-three-dropdown");
        let itemLinksContainer = itemDropdown.find(".dropdown-links-container");
        $this.on({
            mouseenter: function () {
                itemDropdown.addClass("show");
                $this.addClass("active");
                itemLinksContainer.addClass("animate");
            },
            mouseleave: function () {
                setTimeout(() => {
                    if ($this.find(".header-three-dropdown:hover").length == 0) {
                        itemDropdown.removeClass("show");
                        $this.removeClass("active");
                        itemLinksContainer.removeClass("animate");
                    }
                }, 100);
            },
        });
    });

    // back to top btn
    $("#back_to_top_btn").on("click", function () {
        $("html, body").animate({scrollTop: 0}, 1000);
    });

    //search bar active state
    desktopHeaderPartTwo
        .find("#desktop_search_form .input-container input")
        .on("focus", function () {
            $(this).closest(".input-container").addClass("active");
        });
    $(document).on("click", function (e) {
        var searchBarInputContainer = desktopHeaderPartTwo.find(
            "#desktop_search_form .input-container"
        );
        var searchBarInput = searchBarInputContainer.find("input");
        var searchBarInput = searchBarInputContainer.find("input");
        if (
            searchBarInput.val() == "" &&
            !(
                $(e.target).is("#desktop_search_form .input-container") ||
                $(e.target).is("#desktop_search_form .input-container *")
            )
        ) {
            searchBarInputContainer.removeClass("active");
        }
    });

    // fav icon on product card
    if (productCard.length > 0) {
        $("body").on("click", ".card.card-style-1 .fav-btn", function () {
            startPageLoading();
            const favBtn = $(this);
            const wishlistLink = favBtn.closest(".card.card-style-1").data("wishlist-link");
            $.post(wishlistLink, function (json) {
                endPageLoading();
                if (!json.error) {
                    console.log(json);
                    const action = json.action;
                    if (action === "PRODUCT_ADDED") {
                        favBtn.addClass("active");
                    } else if (action === "PRODUCT_REMOVED") {
                        favBtn.removeClass("active");
                    }
                    updateWishlistNumber(json.productsFavouritesCount);
                    showAlert("success", json.message);
                } else {
                    showAlert("error", json.message);
                }
            });
        });
    }

    // svg icons
    $("i.convert-svg").each(function () {
        var $img = $(this);
        convertSvgToIcon($img);
    });

    body.on("click", "#search_popup .close-search-btn", function () {
        searchPopup.removeClass("show");
        $("body").removeClass("modal-open");
    });

    body.on("click", "header #mobile_header #mobile_search_btn", function () {
        searchPopup.addClass("show");
        $("body").addClass("modal-open");
    });

    mobileHeader.find("#mobile_cart_btn").on("click", function () {
        mobileHeader.find("#mobile_cart").addClass("show");
        $("body").addClass("modal-open");
    });
    mobileHeader.find("#close_cart_btn").on("click", function () {
        mobileHeader.find("#mobile_cart").removeClass("show");
        $("body").removeClass("modal-open");
    });

    // mobile menu
    mobileHeader.find("#mobile_menu_btn").on("click", function () {
        mobileMenuWrapper.addClass("show");
        mobileMenu.addClass("show");
        $("body").addClass("modal-open");
    });
    mobileMenu.find("#close_mobile_menu_btn").on("click", function () {
        mobileMenuWrapper.removeClass("show");
        mobileMenu.removeClass("show");
        $("body").removeClass("modal-open");
    });

    // mobile cart incrementor
    const qtyInput = $(".incrementor-style-2 input[name=qty]");
    $(".incrementor-style-2 button.plus").on("click", function () {
        let newVal = increment($(this).closest(".incrementor-style-2").find(qtyInput));
        $(this).closest(".incrementor-style-2").find(qtyInput).val(newVal);
        $(this).closest(".incrementor-style-2").find("button.minus").prop("disabled", false);
    });

    $(".incrementor-style-2 button.minus").on("click", function () {
        let newVal = decrement($(this).closest(".incrementor-style-2").find(qtyInput));
        if (newVal) {
            $(this).closest(".incrementor-style-2").find(qtyInput).val(newVal);
        }
        if (newVal < 1) {
            $(this).prop("disabled", true);
        }
    });

    // alert
    $(".alert.alert-1").find("button.alert__btn").on("click", function () {
        $(this).closest(".alert.alert-1").removeClass("show");
    });

    // CART HANDLING
    if (productCard.length > 0) {
        productCard.each(function () {
            const cartBtn = $(this).find(".cart-btn");
            const addItemAjaxLink = cartBtn.data("link");

            cartBtn.on("click", function () {
                cartBtn.attr("disabled", true).text("loading..."); //@todo: translate text
                $.post(addItemAjaxLink, function (json) {
                    cartBtn.attr("disabled", false).text("Add to cart"); //@todo: translate text
                    if (!json.error) {
                        // DESKTOP CART
                        const desktopCartDropdown = $("#desktop_cart_dropdown");
                        const itemsContainer = desktopCartDropdown.find(".items-container");
                        const items = itemsContainer.find(".item");
                        const totalCartQty = json.totalCartQuantity
                        const cartItem = json.cartItem;
                        itemsContainer.find("p.desktop-cart-empty-txt").remove();
                        desktopCartDropdown.closest(".icon-container").find("#desktop_cart_items_count").text(totalCartQty);
                        let isItemExist = false;
                        if (items.length > 0) {
                            items.each(function () {
                                const existingItem = $(this);
                                const existingItemId = existingItem.data("item-id");
                                if (existingItemId === cartItem.itemId) {
                                    isItemExist = true;
                                    existingItem.find(".item-quantity").text(`Quantity: ${cartItem.itemQty}`)
                                }
                            })
                        }
                        if (items.length === 0 || !isItemExist) {
                            drawCartItem(cartItem, itemsContainer);
                            $("img.lazy").lazy({
                                effect: "fadeIn",
                            });
                        }
                        // END DESKTOP CART
                        // MOBILE CART
                        const mobileCart = $("#mobile_cart");
                        const mobileItemsContainer = mobileCart.find(".items-container");
                        const mobileCartItems = mobileItemsContainer.find(".cart-item-container");
                        const mobileCartItem = json.cartItem;
                        mobileItemsContainer.find("p.mobile-cart-empty-txt").remove();
                        if (mobileCartItems.length > 0) {
                            mobileCartItems.each(function () {
                                const existingItem = $(this);
                                const existingItemId = existingItem.data("item-id");
                                if (existingItemId === mobileCartItem.itemId) {
                                    existingItem.find("input[name='qty']").val(cartItem.itemQty);
                                }
                            })
                        }
                        if (mobileCartItems.length === 0 || !isItemExist) {
                            drawMobileCartItem(mobileCartItem, mobileItemsContainer);
                            $("img.lazy").lazy({
                                effect: "fadeIn",
                            });
                        }
                        // END MOBILE CART
                        showAlert("success", json.message);
                    } else {
                        showAlert("error", json.message);
                    }
                })
            });
        });
    }

    // remove desktop cart item
    $("body").on("click", "#desktop_cart_dropdown .item .price-btn-container button", function () {
        const removeWholeItemBtn = $(this);
        const desktopCartDropDown = $("#desktop_cart_dropdown");
        const cartItem = removeWholeItemBtn.closest(".item");
        const removeWholeItemLink = removeWholeItemBtn.data("link");
        removeWholeItemBtn.attr("disabled", true).text("loading..."); //@todo: translate text

        $.post(removeWholeItemLink, function (json) {
            removeWholeItemBtn.attr("disabled", false).text("Remove"); //@todo: translate text
            if (!json.error) {
                updateDropDownCartQty(json.newCartTotalQuantity);
                cartItem.remove();
                const itemsCount = desktopCartDropDown.find(".item").length;
                if (itemsCount === 0) {
                    const cartEmptyMsg = $("<p>", {
                        class: "desktop-cart-empty-txt",
                        text: "Your Cart is Empty" //@todo: translate text
                    });
                    cartEmptyMsg.appendTo(desktopCartDropDown.find(".items-container"));
                }

                if ($("#cart_page").length > 0) {
                    const cartPage = $("#cart_page");
                    adjustSummaryBox(json.newCartTotalQuantity, json.cartTotal, json.cartGrandTotal);
                    cartPage.find("#cart_form .cart-item-container").each(function () {
                        const cartItemContainer = $(this);
                        const cartItem = cartItemContainer.find(".cart-item");
                        if (cartItem.data("item-id") === json.cartItemId) {
                            cartItemContainer.remove();
                            if (cartPage.find("#cart_form .cart-item-container").length === 0) {
                                $("#cart_summary").remove();
                                const cartEmptyMsgInPage = $("<h5>", {
                                    class: "text-center py-5 mb-0",
                                    text: "Your Cart is Empty" //@todo: translate text
                                });
                                cartEmptyMsgInPage.appendTo("#cart_page .container");
                            }
                        }
                    })
                }
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    });

    // remove mobile cart item
    body.on("click", "#mobile_cart .remove-item-btn", function () {
        const removeWholeItemBtn = $(this);
        const mobileCart = $("#mobile_cart");
        const mobileCartItem = removeWholeItemBtn.closest(".cart-item-container");
        const removeWholeItemLink = removeWholeItemBtn.data("link");
        startPageLoading();

        $.post(removeWholeItemLink, function (json) {
            endPageLoading();
            if (!json.error) {
                updateDropDownCartQty(json.newCartTotalQuantity);
                mobileCartItem.remove();
                const itemsCount = mobileCart.find(".cart-item-container").length;
                if (itemsCount === 0) {
                    const cartEmptyMsg = $("<p>", {
                        class: "mobile-cart-empty-txt",
                        text: "Your Cart is Empty" //@todo: translate text
                    });
                    cartEmptyMsg.appendTo(mobileCart.find(".items-container"));
                }

                if ($("#cart_page").length > 0) {
                    const cartPage = $("#cart_page");
                    adjustSummaryBox(json.newCartTotalQuantity, json.cartTotal, json.cartGrandTotal);
                    cartPage.find("#cart_form .cart-item-container").each(function () {
                        const cartItemContainer = $(this);
                        const cartItem = cartItemContainer.find(".cart-item");
                        if (cartItem.data("item-id") === json.cartItemId) {
                            cartItemContainer.remove();
                            if (cartPage.find("#cart_form .cart-item-container").length === 0) {
                                $("#cart_summary").remove();
                                const cartEmptyMsgInPage = $("<h5>", {
                                    class: "text-center py-5 mb-0",
                                    text: "Your Cart is Empty" //@todo: translate text
                                });
                                cartEmptyMsgInPage.appendTo("#cart_page .container");
                            }
                        }
                    })
                }
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    });


    // add one mobile cart item
    body.on("click", "#mobile_cart .incrementor.incrementor-style-2 button.plus", function () {
        startPageLoading();
        const plusBtn = $(this);
        const incrementorInput = plusBtn.closest(".incrementor").find("input[name='qty']");
        const addItemLink = plusBtn.data("link");
        $.post(addItemLink, function (json) {
            endPageLoading();
            if (!json.error) {
                const addedCartItem = json.cartItem;
                // DESKTOP CART
                updateDropDownCartQty(json.totalCartQuantity);
                const existingCartItems =  $("#desktop_cart_dropdown").find(".items-container .item");
                existingCartItems.each(function () {
                    const existingItem = $(this);
                    const existingItemId = existingItem.data("item-id");
                    if (existingItemId === addedCartItem.itemId) {
                        existingItem.find(".item-quantity").text(`Quantity: ${addedCartItem.itemQty}`)
                    }
                });
                // END DESKTOP CART
                // MOBILE CART
                incrementorInput.val(addedCartItem.itemQty);
                if ($("#cart_page").length > 0) {
                    const cartPageItems = $("#cart_page .cart-item-container");
                    cartPageItems.each(function () {
                        const cartPageItem = $(this);
                        const itemId = cartPageItem.find(".cart-item").data("item-id");
                        if (itemId == addedCartItem.itemId) {
                            cartPageItem.find("input[name='qty']").val(addedCartItem.itemQty);
                        }
                    });
                }
                // END MOBILE CART
                adjustSummaryBox(json.totalCartQuantity, json.cartTotalPrice, json.cartGrandTotal);
                showAlert("success", json.message);
            } else {
                incrementorInput.val(incrementorInput.val() - 1);
                if ($("#cart_page").length > 0) {
                    const cartPageItems = $("#cart_page .cart-item-container");
                    cartPageItems.each(function (cartPageItem) {
                        const itemId = cartPageItem.find(".cart-item").data("item-id");
                        if (itemId == addedCartItem.itemId) {
                            cartPageItem.find("input[name='qty']").val(cartPageItem.find("input[name='qty']").val() - 1);
                        }
                    });
                }
                showAlert("error", json.message);
            }
        });
    });

    // remove one mobile cart item
    body.on("click", "#mobile_cart .incrementor.incrementor-style-2 button.minus", function () {
        startPageLoading();
        const minusBtn = $(this);
        const incrementorInput = minusBtn.closest(".incrementor").find("input[name='qty']");
        const removeOneItemUrl = minusBtn.data("link");

        $.post(removeOneItemUrl, function (json) {
            endPageLoading();
            if (!json.error) {
                // DESKTOP CART
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
                // END DESKTOP CART
                // MOBILE CART
                incrementorInput.val(json.itemQty);
                if ($("#cart_page").length > 0) {
                    const cartPageItems = $("#cart_page .cart-item-container");
                    cartPageItems.each(function () {
                        const cartPageItem = $(this);
                        const itemId = cartPageItem.find(".cart-item").data("item-id");
                        if (itemId == json.itemId) {
                            cartPageItem.find("input[name='qty']").val(json.itemQty);
                        }
                    });
                }
                // END MOBILE CART
                adjustSummaryBox(json.cartTotalQty, json.cartTotalPrice, json.cartGrandTotal);
                showAlert("success", json.message);
            } else {
                incrementorInput.val(incrementorInput.val() + 1);
                if ($("#cart_page").length > 0) {
                    const cartPageItems = $("#cart_page .cart-item-container");
                    cartPageItems.each(function () {
                        const cartPageItem = $(this);
                        const itemId = cartPageItem.find(".cart-item").data("item-id");
                        if (itemId == json.itemId) {
                            cartPageItem.find("input[name='qty']").val(cartPageItem.find("input[name='qty']").val() - 1);
                        }
                    });
                }
                showAlert("error", json.message);
            }
        });
    });


    // DESKTOP SEARCH
    body.on("submit", "#desktop_search_form", function (e) {
        e.preventDefault();
    });

    body.on("click", document, function (e) {
        if (
            $(e.target).is("#website_search_dropdown")
            ||
            $(e.target).is("#website_search_dropdown *")
        ) {
            return;
        }

        $("#website_search_dropdown").removeClass("show");
    });

    body.on("keyup", "#desktop_search_form .input-container input", function () {
        const input = $(this);
        const inputValue = input.val();
        const form = input.closest("#desktop_search_form");
        const link = form.data("link");
        const searchDropdown = form.find("#website_search_dropdown");
        const ul = searchDropdown.find("ul");
        ul.empty();
        searchDropdown.addClass("show");
        startLoadingComponent(searchDropdown);

        if (inputValue.trim().length < 2) {
            const atLeastTwoCharactersMessage = $("<p>", {
                class: "text-center py-4 mb-0",
                text: "Please Enter at least 2 characters"  //@todo: translate text
            });
            atLeastTwoCharactersMessage.appendTo(ul);
            endLoadingComponent(searchDropdown);
            return;
        }

        currentSearchRequest = $.ajax({
            url: link,
            data: {searchKeyword: inputValue},
            dataType: "json",
            method: "post",
            beforeSend: function () {
                if (currentSearchRequest != null) {
                    currentSearchRequest.abort();
                }
            },
            success: function (json) {
                endLoadingComponent(searchDropdown);
                const results = json.results;
                if (results.length === 0) {
                    const noResultsMessage = $("<p>", {
                        class: "text-center py-4 mb-0",
                        text: "No Results Found"  //@todo: translate text
                    });
                    noResultsMessage.appendTo(ul);
                    return;
                }

                drawDesktopSearchResults(results, ul);
            },
        });
    });

    body.on("submit", "#edit_profile_form", function (e) {
        e.preventDefault();
        startPageLoading();
        const editProfileForm = $(this);
        const link = editProfileForm.attr("action");
        const data = {
            name: editProfileForm.find("input[name='name']").val(),
            email: editProfileForm.find("input[name='email']").val(),
            phone: editProfileForm.find("input[name='phone']").val(),
        }

        $.post(link, data, function (json) {
            endPageLoading();
            if (!json.error) {
                showAlert("success", json.message);
            } else {
                const errors = json.messages;
                errors.forEach(function (errorMessage) {
                    showAlert("error", errorMessage);
                });
            }
        });
    });
});

const drawDesktopSearchResults = (results, listContainer) => {
    results.forEach(function (result) {
        const li = $("<li>");
        const searchResultLink = $("<a>").attr({
            class: "search-result__link",
            href: result.productUrl,
        });
        const imageContainer = $("<div>", {class: "img-container"});
        const image = $("<img />", {
            alt: result.title,
            loading: "lazy"
        }).attr({
            src: result.imageUrl,
        });
        const resultTitle = $("<span>", {class: "result__title", text: result.title});

        image.appendTo(imageContainer);
        imageContainer.appendTo(searchResultLink);
        resultTitle.appendTo(searchResultLink);
        searchResultLink.appendTo(li);
        li.appendTo(listContainer);
    });
};

const drawCartItem = (cartItem, itemsContainer) => {
    const item = $("<div>", {
        class: "item",
    }).attr({
        "data-item-id": cartItem.itemId
    });
    const itemImageContainer = $("<div>", {
        class: "item-img-container",
    });
    const itemImage = $("<img />", {
        class: "lazy",
        alt: cartItem.itemTitle,
    }).attr({
        "data-src": cartItem.itemImageUrl,
    });
    const itemDetailsContainer = $("<div>", {
        class: "item-details-container",
    });
    const itemTextContainer = $("<div>", {
        class: "item-text-container",
    });
    const itemTitle = $("<a>", {
        text: cartItem.itemTitle
    }).attr({
        class: "item-title",
        href: cartItem.itemLink,
    });
    const itemQty = $("<p>", {
        class: "item-quantity mb-0",
        text: `Quantity: ${cartItem.itemQty}`
    });
    const PriceBtnContainer = $("<div>", {
        class: "price-btn-container",
    });
    const itemPrice = $("<p>", {
        class: "item-price mb-0",
        text: cartItem.itemPrice
    });
    const removeBtn = $("<button>", {text: "Remove"}).attr({ // @todo: translate text
        type: "button",
        class: "btn btn-style-1 hover-effect",
        "data-link": cartItem.removeWholeItemUrl,
    });

    itemImage.appendTo(itemImageContainer);
    itemImageContainer.appendTo(item);
    itemTitle.appendTo(itemTextContainer);
    itemQty.appendTo(itemTextContainer);
    itemTextContainer.appendTo(itemDetailsContainer);
    itemPrice.appendTo(PriceBtnContainer);
    removeBtn.appendTo(PriceBtnContainer);
    PriceBtnContainer.appendTo(itemDetailsContainer);
    itemDetailsContainer.appendTo(item);
    item.appendTo(itemsContainer);
}

const drawMobileCartItem = (cartItem, itemsContainer) => {
    const cartItemContainer = $("<div>", {
        class: "cart-item-container",
    }).attr({
        "data-item-id": cartItem.itemId
    });
    const removeItemBtn = $("<button>").attr({
        type: "button",
        class: "btn remove-item-btn",
        "data-link": cartItem.removeWholeItemUrl,
    });
    const removeBtnIcon = $("<i>", {class: "convert-svg"}).attr({"data-src": appIconsPaths.xIcon});
    const itemImgContainer = $("<div>", {class: "item-img-container"});
    const itemImg = $("<img />", {alt: cartItem.itemTitle, class: "lazy"}).attr({"data-src": cartItem.itemImageUrl});
    const itemDetails = $("<div>", {class: "item-details"});
    const itemTitle = $("<h2>", {class: "item-title", text: cartItem.itemSubcategory});
    const itemDescription = $("<a>", {class: "item-title", text: cartItem.itemTitle}).attr({
        href: cartItem.itemLink
    });
    const pricingContainer = $("<div>", {class: "pricing-container"});
    const incrementor = $("<div>", {class: "input-group incrementor incrementor-style-2"});
    const minusBtn = $("<button>").attr({
        type: "button",
        class: "btn minus", // @todo: add btn link to remove an item from the cart
    });
    const minusIcon = $("<i>", {class: "convert-svg"}).attr({"data-src": appIconsPaths.minusIcon});
    const input = $("<input />").attr({
        type: "number",
        value: cartItem.itemQty,
        name: "qty"
    });
    const plusBtn = $("<button>").attr({
        type: "button",
        class: "btn plus", // @todo: add btn link to remove an item from the cart
    });
    const plusIcon = $("<i>", {class: "convert-svg"}).attr({"data-src": appIconsPaths.plusIcon});
    const itemPrice = $("<p>", {class: "item-price", text: cartItem.itemPrice});

    removeBtnIcon.appendTo(removeItemBtn);
    removeItemBtn.appendTo(cartItemContainer);
    itemImg.appendTo(itemImgContainer);
    itemImgContainer.appendTo(cartItemContainer);
    itemTitle.appendTo(itemDetails);
    itemDescription.appendTo(itemDetails);
    minusIcon.appendTo(minusBtn);
    minusBtn.appendTo(incrementor);
    input.appendTo(incrementor);
    plusIcon.appendTo(plusBtn);
    plusBtn.appendTo(incrementor);
    incrementor.appendTo(pricingContainer);
    itemPrice.appendTo(pricingContainer);
    pricingContainer.appendTo(itemDetails);
    itemDetails.appendTo(cartItemContainer);

    cartItemContainer.appendTo(itemsContainer);
    cartItemContainer.find("i.convert-svg").each(function () {
        var $img = $(this);
        convertSvgToIcon($img);
    });
};

const updateDropDownCartQty = (totalQty) => {
    $("#desktop_cart_items_count").text(totalQty);
}

function convertSvgToIcon($img) {
    var imgID = $img.attr("id");
    var imgClass = $img.attr("class");
    var imgURL = $img.attr("data-src");
    if (typeof imgURL === "undefined") {
        return false;
    }

    $svg = getSvgIconByUrl(imgURL);
    if ($svg == null) {
        return false;
    }

    // Add replaced image's ID to the new SVG
    if (typeof imgID !== "undefined") {
        $svg = $svg.attr("id", imgID);
    }
    // Add replaced image's classes to the new SVG
    if (typeof imgClass !== "undefined") {
        $svg = $svg.attr("class", imgClass + " replaced-svg");
    }
    $img.replaceWith($svg);
}

function getSvgIconByUrl(imgURL) {
    var $svg = null;

    $.ajax({
        url: imgURL,
        type: "get",
        dataType: "xml",
        async: false,
        success: function (data) {
            $svg = $(data).find("svg");

            // Remove any invalid XML tags as per http://validator.w3.org
            $svg = $svg.removeAttr("xmlns:a");

            // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
            if (!$svg.attr("viewBox") && $svg.attr("height") && $svg.attr("width")) {
                $svg.attr(
                    "viewBox",
                    "0 0 " + $svg.attr("height") + " " + $svg.attr("width")
                );
            }
        },
    });

    return $svg;
}

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

const showAlert = (type, message) => {
    const alert = $(".alert.alert-1");
    alert.addClass(type);
    alert.find(".alert__message").text(message);
    alert.addClass("show");
};

const updateWishlistNumber = (newCount) => {
    $("#desktop_wishlist_items_count").text(newCount);
}

const startPageLoading = () => {
    body.addClass("loading");
}

const endPageLoading = () => {
    body.removeClass("loading");
}

const startLoadingComponent = (component) => {
    component.addClass("component-loading");
}

const endLoadingComponent = (component) => {
    component.removeClass("component-loading");
}

const adjustSummaryBox = (cartTotalQty, cartTotalPrice, CartGrandTotalPrice) => {
    const summaryBox = $("#cart_summary");
    summaryBox.find(".items-number").text(cartTotalQty);
    summaryBox.find(".subtotal-amount").text(cartTotalPrice);
    summaryBox.find(".cart-total").text(CartGrandTotalPrice);
};
