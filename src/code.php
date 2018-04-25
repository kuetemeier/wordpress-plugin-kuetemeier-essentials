<?php

die("not for now!");


/******************************************/



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

