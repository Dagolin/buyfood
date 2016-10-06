<?php
/**
 * Abstract Flavours Widget Class
 *
 * @author      FlavoursThemes
 * @category    Widgets
 * @package     FlavoursThemes/Abstracts
 * @version     2.1.0
 * @extends     WP_Widget
 */
abstract class tmFlavours_Widget extends WP_Widget {
	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	}

	/**
	 * get_cached_widget function.
	 */
	function get_cached_widget( $args ) {

		$cache = wp_cache_get( apply_filters( 'tmFlavours_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo esc_html($cache[ $args['widget_id'] ]);
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 * @param string $content
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( apply_filters( 'tmFlavours_cached_widget_id', $this->widget_id ), $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'tmFlavours_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if ( ! $this->settings ) {
			return $instance;
		}

		foreach ( $this->settings as $key => $setting ) {

			if ( isset( $new_instance[ $key ] ) ) {
				if ( current_user_can('unfiltered_html') ) {
					$instance[$key] =  $new_instance[$key];
				}
				else {
					$instance[$key] = stripslashes( wp_filter_post_kses( addslashes($new_instance[$key]) ) );
				}
			} elseif ( 'checkbox' === $setting['type'] ) {
				$instance[ $key ] = 0;
			}
		}
		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	public function form( $instance ) {

		if ( ! $this->settings ) {
			return;
		}

		foreach ( $this->settings as $key => $setting ) {

			$value   = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {
				case "text" :
					?>
					<p>
						<label for="<?php echo esc_html($this->get_field_id( $key )); ?>"><?php echo esc_html($setting['label']); ?></label>
						<input class="widefat" id="<?php echo esc_html( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_html($this->get_field_name( $key )); ?>" type="text" value="<?php echo esc_html( $value ); ?>" />
					</p>
					<?php
					break;

				case "number" :
					$number_step = isset($setting['step']) ? $setting['step'] : '1';
					$number_min = isset($setting['min']) ? ' min="' . esc_html($setting['min']) . '"' : '';
					$number_max = isset($setting['max']) ? ' max="' . esc_html($setting['max']) . '"' : '';
					?>
					<p>
						<label for="<?php echo esc_html($this->get_field_id( $key )); ?>"><?php echo esc_html($setting['label']); ?></label>
						<input class="widefat" id="<?php echo esc_html( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_html($this->get_field_name( $key )); ?>" type="number" step="<?php echo esc_html( $number_step ); ?>"<?php echo sprintf('%s', $number_min); ?><?php echo sprintf('%s', $number_max); ?> value="<?php echo esc_html( $value ); ?>" />
					</p>
					<?php
					break;

				case "select" :
					?>
					<p>
						<label for="<?php echo esc_html($this->get_field_id( $key )); ?>"><?php echo esc_html($setting['label']); ?></label>
						<select class="widefat" id="<?php echo esc_html( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_html($this->get_field_name( $key )); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_html( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
					break;

				case "checkbox" :
					?>
					<p>
						<input id="<?php echo esc_html( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_html( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo esc_html($this->get_field_id( $key )); ?>"><?php echo esc_html($setting['label']); ?></label>
					</p>
					<?php
					break;
                case "multi-select" :
                    ?>
                    <p>
                        <label for="<?php echo esc_html( $this->get_field_id( $key ) ); ?>"><?php echo esc_html($setting['label']); ?></label>
                        <input name="<?php echo esc_html($this->get_field_name( $key )); ?>" type="hidden" value="<?php echo esc_html( $value ); ?>" />
                        <select multiple class="widefat widget-select2" id="<?php echo esc_html( $this->get_field_id( $key ) ); ?>" data-value="<?php echo esc_html($value) ?>">
                            <?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
                                <option value="<?php echo esc_html( $option_key ); ?>"><?php echo esc_html( $option_value ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <?php
                    break;

				case "icon":
					?>
					<div style="margin: 13px 0">
						<label for="<?php echo esc_html($this->get_field_id( $key )); ?>"><?php echo esc_html($setting['label']); ?> </label>
						<div>
							<input style="width: 145px" type="text" class="input-icon" id="<?php echo esc_html( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_html( $this->get_field_name( $key ) ); ?>" value="<?php echo esc_html($value); ?>">
							<input style="float: right"  title="<?php echo esc_attr_e('Click to browse icon','flavours') ?>" class="browse-icon button-secondary" type="button" value="<?php echo esc_attr_e('Browse...','flavours') ?>" />
							<span style="vertical-align: top;width: 30px; height: 30px" class="icon-preview"><i class="fa <?php echo esc_html( $value );  ?>"></i></span>
						</div>
					</div>
					<?php
					break;
			
				case "text-area":
					?>
					<p>
						<label for="<?php echo esc_html($this->get_field_id( $key )); ?>"><?php echo esc_html($setting['label']); ?></label>
						<textarea class="widefat" rows="8" cols="40" id="<?php echo esc_html( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_html($this->get_field_name( $key )); ?>"><?php echo esc_textarea($value); ?></textarea>
					</p>
					<?php
					break;
			
			}
		}
	}
}




if (!function_exists('tmFlavours_post_thumbnail')) {
    function tmFlavours_post_thumbnail($size = '') {
        $html = '';
        $prefix = 'tmFlavours_';
        $width = '';
        $height = '';
        global $tmFlavours_image_size;
        if (isset($tmFlavours_image_size[$size])) {
            $width = $tmFlavours_image_size[$size]['width'];
            $height = $tmFlavours_image_size[$size]['height'];
        }
  
        switch(get_post_format()) {

            case 'image' :
                $args = array(
                    'size' => $size,
                   'meta_key' => ''
                );
                $image = tmFlavours_get_image($args);

                if (!$image) break;
                $html = tmFlavours_get_image_hover($image,$size, get_permalink(), the_title_attribute('echo=0'),get_the_ID());
                break;
            case 'gallery':
                $images = array();
                if (count($images) > 0) {
                    $data_plugin_options = "data-plugin-options='{\"singleItem\" : true, \"pagination\" : false, \"navigation\" : true, \"autoHeight\" : true}'";
                    $html = "<div class='owl-carousel' $data_plugin_options>";
                    foreach ($images as $image) {

                        if (empty($width) || empty($height)) {
                            $image_src_arr = wp_get_attachment_image_src( $image, $size );
                            if ($image_src_arr) {
                                $image_src = $image_src_arr[0];
                            }
                        } else {
                            $image_src = matthewruddy_image_resize_id($image,$width,$height);
                        }

                        if (!empty($image_src)) {
                            $html .= tmFlavours_get_image_hover($image_src,$size, get_permalink(), the_title_attribute('echo=0'),get_the_ID(),1);
                        }
                    }
                    $html .= '</div>';
                } else {
                    $args = array(
                        'size' => $size,
                        'meta_key' => ''
                    );
                    $image = tmFlavours_get_image($args);
                    if (!$image) break;
                    $html = tmFlavours_get_image_hover($image,$size, get_permalink(), the_title_attribute('echo=0'),get_the_ID());
                }
                break;
            case 'video':
                $video = array();
                if (!is_single()) {
                    $args = array(
                        'size' => $size,
                        'meta_key' => ''
                    );
                    $image = tmFlavours_get_image($args);
                    if (!$image) {
                        if (count($video) > 0) {
                            $html .= '<div class="embed-responsive embed-responsive-16by9 embed-responsive-' . $size . '">';
                            $video = $video[0];
                            // If URL: show oEmbed HTML
                            if (filter_var($video, FILTER_VALIDATE_URL)) {
                                $args = array(
                                    'wmode' => 'transparent'
                                );
                                $html .= wp_oembed_get($video, $args);
                            } // If embed code: just display
                            else {
                                $html .= $video;
                            }
                            $html .= '</div>';
                        }
                    } else {
                        $video = $video[0];
                        if (filter_var($video, FILTER_VALIDATE_URL)) {
                            $html .= tmFlavours_get_video_hover($image, get_permalink(), the_title_attribute('echo=0'), $video);
                        } else {
                            $html .= '<div class="embed-responsive embed-responsive-16by9 embed-responsive-' . $size . '">';
                            $html .= $video;
                            $html .= '</div>';
                        }
                    }
                } else {
                    if (count($video) > 0) {
                        $html .= '<div class="embed-responsive embed-responsive-16by9 embed-responsive-' . $size . '">';
                        $video = $video[0];
                        // If URL: show oEmbed HTML
                        if (filter_var($video, FILTER_VALIDATE_URL)) {
                            $args = array(
                                'wmode' => 'transparent'
                            );
                            $html .= wp_oembed_get($video, $args);
                        } // If embed code: just display
                        else {
                            $html .= $video;
                        }
                        $html .= '</div>';
                    }
                }
                break;
            case 'audio':
                $audio =array();
                if (count($audio) > 0) {
                    $audio = $audio[0];
                    if (filter_var($audio, FILTER_VALIDATE_URL)) {
                        $html .= wp_oembed_get($audio);
                        $title = esc_html(get_the_title());
                        $audio = esc_url($audio);
                        if (empty($html)) {
                            $id = uniqid();
                            $html .= "<div data-player='$id' class='jp-jplayer' data-audio='$audio' data-title='$title'></div>";
                            $html .= tmFlavours_jplayer($id);
                        }
                    } else {
                        $html .= $audio;
                    }
                    $html .= '<div style="clear:both;"></div>';
                }
                break;
            default:
                $args = array(
                    'size' => $size,
                    'meta_key' => ''
                );
                $image = tmFlavours_get_image($args);
                if (!$image) break;
                $html = tmFlavours_get_image_hover($image,$size, get_permalink(), the_title_attribute('echo=0'),get_the_ID());
                break;
        }
        return $html;
    }
}





/*================================================
GET POST IMAGE
================================================== */
if (!function_exists('tmFlavours_get_image')) {
    function tmFlavours_get_image($args) {
        $default = apply_filters(
            'tmFlavours_get_image_default_args',
            array(
                'post_id'  => get_the_ID(),
                'size'    => '',
                'width'    => '',
                'height'   => '',
                'attr'     => '',
                'meta_key' => '',
                'scan'     => false,
                'default'  => ''
            )
        );


        $args = wp_parse_args( $args, $default );
        $size = $args['size'];



        $width = '';
        $height = '';

        global $tmFlavours_image_size;
        if (isset($tmFlavours_image_size[$size])) {
            $width = $tmFlavours_image_size[$size]['width'];
            $height = $tmFlavours_image_size[$size]['height'];
        }



        if ( ! $args['post_id'] ) {
            $args['post_id'] = get_the_ID();
        }

        // Get image from cache
        $key         = md5( serialize( $args ) );
        $image_cache = wp_cache_get( $args['post_id'], 'tmFlavours_get_image' );

        if ( ! is_array( $image_cache ) ) {
            $image_cache = array();
        }


        if ( empty( $image_cache[$key] ) ) {

            $image_src = '';

            // Get post thumbnail
            if (has_post_thumbnail($args['post_id'])) {
                $post_thumbnail_id   = get_post_thumbnail_id($args['post_id']);


                if (empty($width) || empty($height)) {
                    $image_src_arr = wp_get_attachment_image_src( $post_thumbnail_id, $size );
                    if ($image_src_arr) {
                        $image_src = $image_src_arr[0];
                    }
                } else {
                    $image_src = matthewruddy_image_resize_id($post_thumbnail_id,$width,$height);
                }
            }

            // Get the first image in the custom field
            if ((!isset($image_src) || empty($image_src))  && $args['meta_key']) {
                $post_thumbnail_id = tmFlavours_get_post_meta( $args['post_id'], $args['meta_key'], true );
                if ( $post_thumbnail_id ) {

                    if (empty($width) || empty($height)) {
                        $image_src_arr = wp_get_attachment_image_src( $post_thumbnail_id, $size );
                        if ($image_src_arr) {
                            $image_src = $image_src_arr[0];
                        }
                    } else {
                        $image_src = matthewruddy_image_resize_id($post_thumbnail_id,$width,$height);
                    }
                }
            }

            // Get the first image in the post content
            if ((!isset($image_src) || empty($image_src)) && ($args['scan'])) {
                preg_match( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $args['post_id'] ), $matches );
                if ( ! empty( $matches ) ){
                    $image_src  = $matches[1];
                }
            }

            // Use default when nothing found
            if ( (!isset($image_src) || empty($image_src)) && ! empty( $args['default'] ) ){
                if ( is_array( $args['default'] ) ){
                    $image_src  = @$args['src'];
                } else {
                    $image_src = $args['default'];
                }
            }

            if (!isset($image_src) || empty($image_src)) {
                return false;
            }
            $image_cache[$key] = $image_src;
            wp_cache_set( $args['post_id'], $image_cache, 'tmFlavours_get_image' );
        } else {
            $image_src = $image_cache[$key];
        }
        $image_src = apply_filters( 'tmFlavours_get_image', $image_src, $args );
        return $image_src;
    }
}

/*================================================
GET IMAGE HOVER
================================================== */
if (!function_exists('tmFlavours_get_image_hover')) {
    function tmFlavours_get_image_hover($image,$size, $url, $title, $post_id,$gallery = 0) {
        $attachment_id = tmFlavours_get_attachment_id_from_url($image);

        $image_full_arr = wp_get_attachment_image_src($attachment_id,'full');

        $image_full = $image;

        if (isset($image_full_arr)) {
            $image_full = $image_full_arr[0];
        }



	    $width = '';
	    $height = '';

	    global $tmFlavours_image_size;
	    if (isset($tmFlavours_image_size[$size])) {
		    $width = $tmFlavours_image_size[$size]['width'];
		    $height = $tmFlavours_image_size[$size]['height'];
	    } else {
		    global $_wp_additional_image_sizes;
		    if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			    $width = get_option( $size . '_size_w' );
			    $height = get_option( $size . '_size_h' );

		    } elseif ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
			    $width = $_wp_additional_image_sizes[ $size ]['width'];
			    $height = $_wp_additional_image_sizes[ $size ]['height'];
		    }
	    }

         $prettyPhoto = 'prettyPhoto';
        if ($gallery == 1) {
            $prettyPhoto='prettyPhoto[blog_'.$post_id.']';
        }
            
	    if (empty($width) || empty($height)) {
		    return sprintf('<div class="entry-thumbnail">
                        <a href="%1$s" title="%2$s" class="entry-thumbnail_overlay">
                            <img class="img-responsive" src="%3$s" alt="%2$s"/>
                        </a>
                       
                      </div>',
			    esc_url($url),
			    $title,
			    esc_url($image),
			    $image_full
			  
		    );
	    } else {
		    return sprintf('<div class="entry-thumbnail">
                        <a href="%1$s" title="%2$s" class="entry-thumbnail_overlay">
                            <img width="%6$s" height="%7$s" class="img-responsive" src="%3$s" alt="%2$s"/>
                        </a>
                        
                      </div>',
			    esc_url($url),
			    $title,
			    esc_url($image),
			    $image_full,
			    $prettyPhoto,
			    $width,
			    $height
		    );
	    }


    }
}

if (!function_exists('tmFlavours_get_video_hover')) {
    function tmFlavours_get_video_hover($image, $url, $title, $video_url) {
        return sprintf('<div class="entry-thumbnail">
                        <a class="entry-thumbnail_overlay" href="%1$s" title="%2$s">
                            <img class="img-responsive" src="%3$s" alt="%2$s"/>
                        </a>
                       
                      </div>',
            esc_url($url),
            $title,
            $image,
            $video_url
        );
    }
}

/*================================================
GET ATTACHMENT ID FROM URL
================================================== */
if (!function_exists('tmFlavours_get_attachment_id_from_url')) {
    function tmFlavours_get_attachment_id_from_url($attachment_url = '') {
        global $wpdb;
        $attachment_id = false;

        // If there is no url, return.
        if ( '' == $attachment_url )
            return;

        // Get the upload directory paths
        $upload_dir_paths = wp_upload_dir();

        // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
        if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

            // If this is the URL of an auto-generated thumbnail, get the URL of the original image
            $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

            // Remove the upload path base directory from the attachment URL
            $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

            // Finally, run a custom database query to get the attachment ID from the modified attachment URL
            $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

        }
        return $attachment_id;
    }
}



/*================================================
GET POST META
================================================== */
if ( !function_exists( 'tmFlavours_get_post_meta' ) ) {
	function tmFlavours_get_post_meta( $id, $key = "", $single = false ) {

	}
}