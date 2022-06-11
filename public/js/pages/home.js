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

    $("#user_feedback_form").on("submit", function (event) {
        event.preventDefault();
        const form = $(this);
        const url = form.attr("action");
        const data = collectFeedbackFormData(form);
        $.post(url, data, function (json) {
            console.log(json);
            if (!json.error) {
                $("#userFeedbackModal").modal("hide");
                showAlert("success", json.message);
            } else {
                showAlert("error", json.message);
            }
        });
    })
});

const swiperScrollBarHandler = (scrollBar, currentSlideNumber, slidesCount) => {
    const scrollBarNewWidth = `${(currentSlideNumber / slidesCount) * 100}%`;
    scrollBar.css({width: scrollBarNewWidth});
};

const collectFeedbackFormData = (formElement) => {
    const rate = formElement.find("input[name=feedback]:checked").val();
    const category = formElement.find("select[name=category]").val();

    return {
        rate,
        category
    };
};
