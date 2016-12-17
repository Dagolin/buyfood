/**
 * Created by user on 2016/12/18.
 */
jQuery(document).ready(function($) {
    $('.image-thumbnails').on('click', function(){
        $('.woocommerce-main-image').removeClass('ywcfav_has_featured');
        $('.ywcfav_video_content ').addClass('ywcfav_has_featured');
    });

    $('.video-thumbnails').on('click', function(){
        $('.woocommerce-main-image').addClass('ywcfav_has_featured');
        $('.ywcfav_video_content ').removeClass('ywcfav_has_featured');
    });
});