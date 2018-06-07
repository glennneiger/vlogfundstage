<?php
add_filter( 'wpmdb_preserved_options', function( $options ) {
$options[] = 'woocommerce_stripe_settings';
return $options;
});
