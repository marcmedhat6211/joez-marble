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

  // fav icon
  productDetailsPage
    .find(".swiper-container.main-images-swiper .fav-btn")
    .on("click", function (e) {
      e.preventDefault();
      $(this).toggleClass("active");
    });

  // colors
  const colorsContainer = productDetailsPage.find(
    ".product-main-info-container .colors-container"
  );
  const colors = colorsContainer.find(".color");
  colors.each(function () {
    const bgColor = $(this).data("color");
    $(this).css("background", bgColor);
  });
});
