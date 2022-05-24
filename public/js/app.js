const desktopHeader = $("header #desktop_header");
const desktopHeaderPartOne = desktopHeader.find("#header_part_one");
const desktopHeaderPartTwo = desktopHeader.find("#header_part_two");
const desktopHeaderPartThree = desktopHeader.find("#header_part_three");
const mobileHeader = $("header #mobile_header");
const productCard = $(".card.card-style-1");
const mobileMenuWrapper = $("#mobile_menu_wrapper");
const mobileMenu = $("#mobile_menu");
const searchPopup = $("#search_popup");
$(document).ready(function () {
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
        if (productCard.length > 1) {
            productCard.each(function () {
                $(this)
                    .find(".fav-btn")
                    .on("click", function (e) {
                        e.preventDefault();
                        $(this).toggleClass("active");
                    });
            });
        } else {
            productCard.find(".fav-btn").on("click", function (e) {
                e.preventDefault();
                $(this).toggleClass("active");
            });
        }
    }

    // svg icons
    $("i.convert-svg").each(function () {
        var $img = $(this);
        convertSvgToIcon($img);
    });

    searchPopup.find(".close-search-btn").on("click", function () {
        searchPopup.removeClass("show");
        $("body").removeClass("modal-open");
    });

    mobileHeader.find("#mobile_search_btn").on("click", function () {
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

    // Cart incrementor
    const mobileCartIncrementor = $(".incrementor-style-2");
    const qtyInput = $("input[name=qty]");
    mobileCartIncrementor.find("button.plus").on("click", function () {
        let newVal = increment(
            $(this).closest(mobileCartIncrementor).find(qtyInput)
        );
        $(this).closest(mobileCartIncrementor).find(qtyInput).val(newVal);
        $(this)
            .closest(mobileCartIncrementor)
            .find("button.minus")
            .prop("disabled", false);
    });

    mobileCartIncrementor.find("button.minus").on("click", function () {
        let newVal = decrement(
            $(this).closest(mobileCartIncrementor).find(qtyInput)
        );
        if (newVal) {
            $(this).closest(mobileCartIncrementor).find(qtyInput).val(newVal);
        }
        if (newVal == 1) {
            $(this).prop("disabled", true);
        }
    });

    // alert
    $(".alert.alert-1").find("button.alert__btn").on("click", function () {
        $(this).closest(".alert.alert-1").removeClass("show");
    });
});

// CART HANDLING
if (productCard.length > 0) {
    productCard.each(function () {
        const cartBtn = $(this).find(".cart-btn");
        const addItemAjaxLink = cartBtn.data("link");

        cartBtn.on("click", function () {
            cartBtn.text("loading...");
            $.post(addItemAjaxLink, function (json) {
                if (!json.error) {
                    const desktopCartDropdown = $("#desktop_cart_dropdown");
                    const itemsContainer = desktopCartDropdown.find(".items-container");
                    const items = itemsContainer.find(".item");
                    const totalCartQty = json.totalCartQuantity
                    const cartItem = json.cartItem;

                    desktopCartDropdown.closest(".icon-container").find("#desktop_cart_items_count").text(totalCartQty);
                    let isItemExist = false;
                    if (items.length > 0) {
                        items.each(function() {
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
                    showAlert("success", json.message);
                } else {
                    showAlert("error", json.message);
                }
            })
            cartBtn.text("Add to cart");
        });
    });
}

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
        href: "#", //TODO: add right link
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
        text: `${cartItem.itemPrice} EGP` //@todo: change the egp to be dynamic
    });
    const removeBtn = $("<button>", {text: "Remove"}).attr({ // @todo: translate text
        type: "button",
        class: "btn btn-style-1 hover-effect",
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
// END CART HANDLING

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
