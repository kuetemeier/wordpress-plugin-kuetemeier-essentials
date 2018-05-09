<?php

die("not for now!");


/******************************************/
/* This is code for inspiration           */


// remove Font-Awesome Generate Press

add_action( 'wp_enqueue_scripts', 'generate_remove_fontawesome', 100 );
function generate_remove_fontawesome() {
	wp_dequeue_style( 'fontawesome' );
}

//* TN Dequeue Styles - Remove Font Awesome from WordPress theme
add_action( 'wp_print_styles', 'tn_dequeue_font_awesome_style' );
function tn_dequeue_font_awesome_style() {
      wp_dequeue_style( 'fontawesome' );
      wp_deregister_style( 'fontawesome' );
  if ( !is_admin() && !is_user_logged_in() ) {
      wp_deregister_style( 'elementor-icons' );
  	  wp_deregister_style( 'font-awesome' );
  }
//	  wp_deregister_style( 'font-awesome-essentials' );
}

/*
function kuetemeier_plugins_loaded() {
    // Do your stuff
      wp_dequeue_style( 'fontawesome' );
      wp_deregister_style( 'fontawesome' );
      wp_deregister_style( 'elementor-icons' );
      wp_deregister_style( 'font-awesome' );
}

add_action( 'plugins_loaded', 'kuetemeier_plugins_loaded', 100 );
*/

/************************************************************ */

if ( version_compare( $GLOBALS['wp_version'], '3.9', '<' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	deactivate_plugins( __FILE__ );
	if ( isset( $_GET['action'] ) && ( $_GET['action'] == 'activate' || $_GET['action'] == 'error_scrape' ) ) {
		exit( sprintf( __( 'This Plugin requires WordPress version %s or greater.', 'kuetemeier-essentials' ), '3.9' ) );
	}
}
