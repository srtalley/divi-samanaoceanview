<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

require_once( dirname( __FILE__ ) . '/includes/class-woocommerce-appointments.php');

/* Add custom functions below */

add_action( 'wp_enqueue_scripts', 'ds_enqueue_assets', 10 );
function ds_enqueue_assets() {

  wp_enqueue_style( 'divi-style-parent', get_template_directory_uri() . '/style.css', array(), et_get_theme_version() );
  wp_enqueue_style( 'divi-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
  
  wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', array('jquery'), wp_get_theme()->get('Version'), true );

}//end function ds_enqueue_assets

function wl ( $log )  {
  if ( true === WP_DEBUG ) {
      if ( is_array( $log ) || is_object( $log ) ) {
          error_log( print_r( $log, true ) );
      } else {
          error_log( $log );
      }
  }
}