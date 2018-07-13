jQuery(document).ready(function ($) {
    $('.bloc-openagenda__slider').slick({
        infinite: true,
        /*autoplay: true,*/
        arrows: true,
        cssEase: 'linear',
        adaptiveHeight: true
    })
    ;
});


jQuery('.bloc-openagenda__slide').each(function (idx, item) {
    var carouselId = "carousel" + idx;
    this.id = carouselId;
    jQuery(this).slick({
        slide: "#" + carouselId +" .slide",
       appendArrows: "#" + carouselId + " .arrows-bottom",

    });
});