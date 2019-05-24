<?php
// returns the path of the request URI without the query string
// see http://php.net/manual/en/function.parse-url.php
// and http://php.net/manual/en/reserved.variables.server.php
// and http://php.net/manual/en/url.constants.php
$request_uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

$is_admin = strpos( $request_uri, '/wp-admin/' );
// add filter in front pages only
if( false === $is_admin ){
	add_filter( 'option_active_plugins', 'kinsta_option_active_plugins' );
}
/**
 * Filters active plugins
 *
 * @param array   $plugins An array of active plugins.
 */
function kinsta_option_active_plugins( $plugins ){
	global $request_uri;
	$is_organization_archive = strpos( $request_uri, '/organizations/' );
  $is_product_page = strpos( $request_uri, '/collaboration/' );
  $is_blog_article = strpos( $request_uri, '/blog/' );
  $is_create_collab_page = strpos( $request_uri, 'create-a-new-youtube-collaboration' );
  $is_edit_collab_page = strpos( $request_uri, '/edit-your-youtube-collaboration/' );
  $is_create_update_page = strpos( $request_uri, '/update-form/' );
  $is_edit_update_page = strpos( $request_uri, '/update-form-edit/' );
	$unnecessary_plugins = array();
	// conditions
	// if this is not true
	// deactivate plugins
	if( false === $is_organization_archive ){
		$unnecessary_plugins[] = 'toolset-maps/toolset-maps-loader.php';
	}
	foreach ( $unnecessary_plugins as $plugin ) {
		$k = array_search( $plugin, $plugins );
		if( false !== $k ){
			unset( $plugins[$k] );
		}
	}
	return $plugins;
}
