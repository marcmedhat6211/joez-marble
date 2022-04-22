var homePageContainer = $("#home_page_container");
$(document).ready(function () {
  // banner swiper
  var bannerSwiper = new Swiper(".swiper-container.banner-swiper", {
    loop: false,
    slidesPerView: 1,
    centeredSlides: true,
    watchSlidesProgress: true,
    effect: "coverflow",
    coverflowEffect: {
      rotate: 30,
      slideShadows: false,
    },
    lazy: {
      preloadImages: false,
      loadPrevNext: true,
      loadPrevNextAmount: 2,
    },
    pagination: {
      el: ".swiper-pagination",
      type: "bullets",
      clickable: true,
    },
    autoplay: {
      delay: 2000,
    },
    breakpoints: {
      991.98: {
        slidesPerView: 1,
        spaceBetween: 0,
      },
    },
  });

  const bannerSlider = $(".swiper-container.banner-swiper");
  const scrollBar = bannerSlider.find(".scrollbar");
  const totalSlidesNumber = bannerSlider.find(".swiper-slide").length;
  swiperScrollBarHandler(scrollBar, 1, totalSlidesNumber);

  bannerSwiper.on("slideChange", function () {
    const currentSlideNumber = bannerSwiper.activeIndex + 1;
    swiperScrollBarHandler(scrollBar, currentSlideNumber, totalSlidesNumber);
  });

  // reviews swiper
  var reviewsSwiper = new Swiper(".swiper-container.reviews-swiper", {
    loop: false,
    slidesPerView: 1,
    spaceBetween: 20,
    lazy: {
      preloadImages: false,
      loadPrevNext: true,
      loadPrevNextAmount: 4,
    },
    navigation: {
      nextEl: ".swiper-btn-next-reviews",
      prevEl: ".swiper-btn-prev-reviews",
    },
    breakpoints: {
      991.98: {
        slidesPerView: 2,
        spaceBetween: 20,
        watchSlidesVisibility: true,
      },
    },
  });
});

const swiperScrollBarHandler = (scrollBar, currentSlideNumber, slidesCount) => {
  const scrollBarNewWidth = `${(currentSlideNumber / slidesCount) * 100}%`;
  scrollBar.css({ width: scrollBarNewWidth });
};
