<?php

function tmFlavours_top_navigation() {
global $flavours_Options;
   
    $html = '';

    if (true || (isset($flavours_Options['login_button_pos']) && $flavours_Options['login_button_pos'] == 'toplinks')) {

        if (is_user_logged_in()) {
            $logout_link = '';
          if ( class_exists( 'WooCommerce' ) ) {
                $logout_link = wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }            $html .= '<li class="menu-item"><a href="' . esc_url($logout_link) . '">' . esc_html__('Logout', 'flavours') . '</a></li>';
        } else {
            $login_link = $register_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
                if ($active_signup != 'none')
                    $register_link = wp_registration_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '"> ' . esc_html__('Login', 'flavours') . '</a></li>';
            if ($register_link) {
                $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '">' . esc_html__('Register', 'flavours') . '</a></li>';
            }
        }
    }
    if(isset($flavours_Options['show_menu_arrow']) && $flavours_Options['show_menu_arrow'])
   {
    $mcls=' show-arrow';
   }
   else
   {
    $mcls='';
   }
    ob_start();
    if ( has_nav_menu( 'toplinks' ) ) :
    
        wp_nav_menu(array(
            'theme_location' => 'toplinks',
            'container' => '',
            'menu_class' => 'top-links1 mega-menu1' .$mcls,
            'before' => '',
            'after' => '',          
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new TmFlavours_top_navwalker
        ));
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());

    if ($output && $html) { 
        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}

function tmFlavours_mobile_top_navigation() {
global $flavours_Options;
   
    $html = '';

    if ((isset($flavours_Options['login_button_pos']) && $flavours_Options['login_button_pos'] == 'toplinks')) {

        if (is_user_logged_in()) {
            $logout_link = '';
          if ( class_exists( 'WooCommerce' ) ) {
                $logout_link = wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }            $html .= '<li class="menu-item"><a href="' . esc_url($logout_link) . '">' . esc_html__('Logout', 'flavours') . '</a></li>';
        } else {
            $login_link = $register_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                 $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
                if ($active_signup != 'none')
                    $register_link = wp_registration_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '">' . esc_html__('Login', 'flavours') . '</a></li>';
            if ($register_link) {
                $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '">' . esc_html__('Register', 'flavours') . '</a></li>';
            }
        }
    }
   if(isset($flavours_Options['show_menu_arrow']) && $flavours_Options['show_menu_arrow'])
   {
    $mcls=' show-arrow';
   }
   else
   {
    $mcls='';
   }
    ob_start();
    if ( has_nav_menu( 'toplinks' ) ) :
        wp_nav_menu(array(
            'theme_location' => 'toplinks',
            'container' => '',
            'menu_class' => 'top-links1 accordion-menu' . $mcls,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new TmFlavours_mobile_navwalker
        ));
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());

    if ($output && $html) {
        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}

function tmFlavours_main_menu() {
    global $flavours_Options;
   
    $html = '';

    if (isset($flavours_Options['login_button_pos']) && $flavours_Options['login_button_pos'] == 'main_menu') {

        if (is_user_logged_in()) {
            $logout_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $logout_link = wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }

           
                $html .= '<li class="menu-item "><a href="' . esc_url($logout_link) . '">' . esc_html__('Logout', 'flavours') . '</a></li>';         
        } 
        else {
            $login_link = $register_link = '';
            if (class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
         
                if ($active_signup != 'none')
                     $register_link = wp_registration_url( get_home_url() );
            }
           
                if ($register_link) {
                    $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '">' . esc_html__('Register', 'flavours') . '</a></li>';
                }
                $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '">' . esc_html__('Login', 'flavours') . '</a></li>';
           
        }
    }
    if(isset($flavours_Options['show_menu_arrow']) && $flavours_Options['show_menu_arrow'])
   {
    $mcls=' show-arrow';
   }
   else
   {
    $mcls='';
   }
    ob_start();
    if ( has_nav_menu('main_menu') ) :     
        $args = array(
        'container' => '',
        'menu_class' => 'main-menu mega-menu' . $mcls,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new TmFlavours_top_navwalker
        );
       
            $args['theme_location'] = 'main_menu';
        
        wp_nav_menu($args);
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());

    if ($output && $html) {

        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}



function tmFlavours_mobile_menu() {
    global $flavours_Options;

    $html = '';
    if (isset($flavours_Options['login_button_pos']) && $flavours_Options['login_button_pos'] == 'main_menu') {
        if (is_user_logged_in()) {
            $logout_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
              $logout_link = wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
            } else {
                $logout_link = wp_logout_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($logout_link) . '">' . esc_html__('Logout', 'flavours') . '</a></li>';
        } else {
            $login_link = $register_link = '';
            if ( class_exists( 'WooCommerce' ) ) {
                $login_link = wc_get_page_permalink( 'myaccount' );
                if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
                    $register_link = wc_get_page_permalink( 'myaccount' );
                }
            } else {
                $login_link = wp_login_url( get_home_url() );
                $active_signup = get_site_option( 'registration', 'none' );
                $active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
                if ($active_signup != 'none')
                    $register_link = wp_registration_url( get_home_url() );
            }
            $html .= '<li class="menu-item"><a href="' . esc_url($login_link) . '">' . esc_html__('Login', 'flavours') . '</a></li>';
            if ($register_link ) {
                $html .= '<li class="menu-item"><a href="' . esc_url($register_link) . '">' . esc_html__('Register', 'flavours') . '</a></li>';
            }
        }
    }

   
    ob_start();
    if ( has_nav_menu( 'main_menu' ) ) :
      
        $args = array(
            'container' => '',
            'menu_class' => 'mobile-menu accordion-menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'fallback_cb' => false,
            'walker' => new TmFlavours_mobile_navwalker
        );
      
            $args['theme_location'] = 'main_menu';
        
        wp_nav_menu($args);
    endif;

    $output = str_replace('&nbsp;', '', ob_get_clean());


    if ($output && $html) {
        $output = preg_replace('/<\/ul>$/', $html . '</ul>', $output, 1);
    }

    return $output;
}


?>