const productDetailsPage = $("#product_details_page");

$(document).ready(function () {
    // thumbs swiper
    const thumbsSwiper = new Swiper(".swiper-container.thumbs-swiper", {
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
    const mainImagesSwiper = new Swiper(".swiper-container.main-images-swiper", {
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

    // filter by materials
    body.on("click", "#product_details_page .materials-container .material", function () {
        startPageLoading();
        const materialBtn = $(this);
        const link = materialBtn.data("link");

        $.get(link, function (json) {
            endPageLoading();
            if (!json.error) {
                console.log(json);
                materialBtn.addClass("active");
                $("#product_details_page .materials-container").find(".material").not(materialBtn).removeClass("active");
                const mainImagesSwiper = $(".swiper-container.main-images-swiper");
                const mainSwiperWrapper = mainImagesSwiper.find(".swiper-wrapper");
                const firstMainSlide = mainImagesSwiper.find(".swiper-slide").first();
                mainSwiperWrapper.empty();

                const thumbsSwiper = $(".swiper-container.thumbs-swiper");
                const thumbSwiperWrapper = thumbsSwiper.find(".swiper-wrapper");
                const firstThumbSlide = thumbSwiperWrapper.find(".swiper-slide").first();
                thumbSwiperWrapper.empty();

                const newImagesPaths = json.images;
                newImagesPaths.forEach(function (path, index) {
                    const newMainSlide = firstMainSlide.clone();
                    newMainSlide.attr("aria-label", `${index + 1} / ${newImagesPaths.length}`);
                    newMainSlide.find(".img-container").attr("href", path);
                    newMainSlide.find("img").attr("src", path);
                    newMainSlide.find("img").attr("alt", json.productTitle);
                    newMainSlide.appendTo(mainSwiperWrapper);
                    if (index !== 0) {
                        newMainSlide.removeClass(".swiper-slide-active");
                    } else if (index === 1) {
                        newMainSlide.addClass("swiper-slide-next");
                    }

                    const newThumbSlide = firstThumbSlide.clone();
                    newThumbSlide.attr("aria-label", `${index + 1} / ${newImagesPaths.length}`);
                    newThumbSlide.find(".thumb-img-container img").attr("src", path);
                    newThumbSlide.find(".thumb-img-container img").attr("alt", json.productTitle);
                    newThumbSlide.appendTo(thumbSwiperWrapper);
                    if (index !== 0) {
                        newThumbSlide.removeClass("swiper-slide-visible swiper-slide-active swiper-slide-thumb-active");
                    } else if (index === 1) {
                        newThumbSlide.addClass("swiper-slide-next");
                    }
                });



                // thumbs swiper
                const newThumbsSwiper = new Swiper(".swiper-container.thumbs-swiper", {
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
                });

                // main images swiper
                const newMainImagesSwiper = new Swiper(".swiper-container.main-images-swiper", {
                    loop: false,
                    slidesPerView: 1,
                    centeredSlides: true,
                    watchSlidesProgress: true,
                    spaceBetween: 0,
                    thumbs: {
                        swiper: newThumbsSwiper,
                    },
                    lazy: {
                        preloadImages: false,
                        loadPrevNext: true,
                        loadPrevNextAmount: 2,
                    },
                });



            } else {
                showAlert("error", json.message);
            }
        })
    });
});
