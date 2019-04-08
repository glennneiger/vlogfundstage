<?php
/*
Plugin Name: instant.page
Plugin URI: https://instant.page/wordpress
Description: Make your site’s pages instant in 1 minute.
Author: Alexandre Dieulot
Version: 1.2.2
Author URI: https://dieulot.fr/
*/

add_action( 'wp_enqueue_scripts', instantpage_wp_enqueue_scripts );
add_filter( 'script_loader_tag', instantpage_script_loader_tag, 10, 2 );

function instantpage_wp_enqueue_scripts() {
  wp_enqueue_script( 'instantpage', plugin_dir_url( __FILE__ ) . 'instantpage-1.2.2.js' );
}

function instantpage_script_loader_tag( $tag, $handle ) {
  if ( 'instantpage' === $handle ) {
    $tag = str_replace( 'text/javascript', 'module', $tag );
  }
  return $tag;
}
