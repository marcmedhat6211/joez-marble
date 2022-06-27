const productDetailsPage = $("#product_details_page");

$(document).ready(function () {
    // thumbs swiper
    let thumbsSwiper = new Swiper(".swiper-container.thumbs-swiper", {
        spaceBetween: 5,
        allowTouchMove: true,
        slidesPerView: 3,
        freeMode: true,
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
        lazy: {
            preloadImages: false,
            loadPrevNext: true,
            loadPrevNextAmount: 2,
        },
        // breakpoints: {
        //   991.98: {
        //     slidesPerView: 6,
        //     spaceBetween: 10,
        //   },
        // },
    });

    // main images swiper
    let mainImagesSwiper = new Swiper(".swiper-container.main-images-swiper", {
        loop: false,
        slidesPerView: 1,
        centeredSlides: true,
        watchSlidesProgress: true,
        spaceBetween: 0,
        thumbs: {
            swiper: thumbsSwiper,
        },
        lazy: {
            preloadImages: false,
            loadPrevNext: true,
            loadPrevNextAmount: 2,
        },
    });

    // related products swiper
    const relatedProductsSwiper = new Swiper(
        ".swiper-container.related-products-swiper",
        {
            loop: false,
            slidesPerView: 1.5,
            freeMode: false,
            watchSlidesProgress: true,
            spaceBetween: 20,
            lazy: {
                preloadImages: false,
                loadPrevNext: true,
                loadPrevNextAmount: 2,
            },
            navigation: {
                nextEl: ".swiper-btn-next",
                prevEl: ".swiper-btn-prev",
            },
            breakpoints: {
                991.98: {
                    slidesPerView: 4,
                    spaceBetween: 10,
                },
            },
        }
    );

    // add to wishlist
    body.on("click", ".swiper-container.main-images-swiper .fav-btn", function () {
        startPageLoading();
        const favBtn = $(this);
        const toggleWishlistLink = favBtn.data("link");
        $.post(toggleWishlistLink, function (json) {
            endPageLoading();
            if (!json.error) {
                updateWishlistNumber(json.productsFavouritesCount);
                const action = json.action;
                if (action === "PRODUCT_ADDED") {
                    favBtn.addClass("active");
                } else if (action === "PRODUCT_REMOVED") {
                    favBtn.removeClass("active");
                }
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    });

    // add to cart
    body.on("click", "#product_details_page .product-main-info-container .add-to-cart-btn", function () {
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
        })
    });
});
