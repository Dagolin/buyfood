<?php
if( !defined( 'ABSPATH' ) )
    exit;
global $post;
$src =  $atts['url'];
$http = is_ssl() ? 'https' : 'http';
list( $video_type, $video_id ) = explode( ':', ywcfav_video_type_by_url( $src ) );


$aspect_ratio = get_option( 'ywcfav_aspectratio' );
$aspect_ratio = explode( '_', $aspect_ratio );

$width_ratio  = $aspect_ratio[0];
$height_ratio = $aspect_ratio[1];
?>

<div class="ywcfav_video_content youtube">
    <div class="ywcfav_video_iframe">
        <a href="<?php echo $src;?>" target="_blank">
            <img src="http://localhost/buyfood/wp-content/themes/flavours/images/play.png" class="play-icon"/>
            </a>
        <a href="<?php echo $src;?>" target="_blank">
            <img src="https://img.youtube.com/vi/<?php echo $video_id; ?>/0.jpg"
             class="video-thumbnails attachment-shop_thumbnail size-shop_thumbnail" alt="p3" title="p3">
            </a>
<!--        <iframe id="video_--><?php //echo $post->ID;?><!--" type="text/html" onload="onYouTubeIframeAPIReady()" src="-->
        <?php //echo $http;?><!--://www.youtube.com/embed/--><?php //echo $video_id;?><!--?enablejsapi=1" frameborder="0"  webkitallowfullscreen mozallowfullscreen allowfullscreen="1">-->
<!--        </iframe>-->
    </div>
</div>
<style>
    .ywcfav_video_iframe { position: relative; }

    .ywcfav_video_iframe .play-icon {
    position: absolute;
    display: block;
    }

</style>
<script type="text/javascript">
    var player,
           img_container = jQuery('.product .images'),
           frame_width = img_container.width()*1,
           frame_height =  Math.round( ( frame_width/<?php echo $width_ratio;?> )*<?php echo $height_ratio;?> );
7
           function onYouTubeIframeAPIReady() {
               if (!player) {
                   player = new YT.Player('video_<?php echo $post->ID;?>',
                       {
                           videoId: '<?php echo $video_id; ?>',
                           events: {
                               'onReady': onPlayerReady,
                               'onStateChange': onPlayerStateChange
                           }
                       });


                   player.setSize(frame_width, frame_height);
               }
            }

            function onPlayerReady( event ){

            	jQuery('.ywcfav_video_iframe').show();

            }

            function onPlayerStateChange( event ){

                 if( event.data === YT.PlayerState.PAUSED ){


                     jQuery('.woocommerce span.onsale:first, .woocommerce-page span.onsale:first').show();

                 }
                else if( event.data == YT.PlayerState.ENDED ){

                     jQuery('.woocommerce span.onsale:first, .woocommerce-page span.onsale:first').show();
                 }
                else if( event.data == YT.PlayerState.PLAYING ){

                	jQuery('.woocommerce span.onsale:first, .woocommerce-page span.onsale:first').hide();
                 }
            }

</script>