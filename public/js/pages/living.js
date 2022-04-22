$(document).ready(function() {
    var categoriesBtnsSwipers = $(".swiper-container.categories-btns-swiper");
    categoriesBtnsSwipers.each(function() {
        var categoriesBtnsSwiper = new Swiper(this, {
            loop: false,
            slidesPerView: 2.5,
            centeredSlides: false,
            spaceBetween: 20,
            breakpoints: {
                991.98: {
                    slidesPerView: 5,
                    spaceBetween: 20,
                },
            },
        });
    })
});