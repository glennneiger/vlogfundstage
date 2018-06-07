<?php
/*
Plugin Name: WP Migrate DB Pro Tweak
Plugin URI: http://github.com/deliciousbrains/wp-migrate-db-pro-tweaks
Description: Preserve woocommerce_stripe_settings during WP Migrate DB Pro migrations
Author: Delicious Brains
Version: 1
*/
add_filter( 'wpmdb_preserved_options', function ( $options ) {
	$options[] = 'woocommerce_stripe_settings';
  $options[] = 'woocommerce_paypal_settings';
	return $options;
} );
