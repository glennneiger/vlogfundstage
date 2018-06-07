<?php
/**
 * Plugin Name: de:comments
 * Plugin URI: http://decomments.com
 * Description:  The most powerful plugin for WordPress comments
 * Version: 2.2.6
 * Author: deco.agency
 * Author URI: http://deco.agency
 * Licence: https://decomments.com/license/
 * Text Domain: decomments
 * Domain Path: languages
 * Copyright 2014-2017 de:comments
 */

/*
* Copyright, deco.agency
* All rights reserved.
*
* Product distribute under the GNUv3 license
*
* 0. Definitions.
*
* “This License” refers to version 3 of the GNU General Public License.
*
* “Copyright” also means copyright-like laws that apply to other kinds of works, such as semiconductor masks.
*
* “The Program” refers to any copyrightable work licensed under this License. Each licensee is addressed as “you”. “Licensees” and “recipients”
*  may be individuals or organizations.
*
* To “modify” a work means to copy from or adapt all or part of the work in a fashion requiring copyright permission, other than the making of
* an exact copy. The resulting work is called a “modified version” of the earlier work or a work “based on” the earlier work.
*
* A “covered work” means either the unmodified Program or a work based on the Program.
*
* To “propagate” a work means to do anything with it that, without permission, would make you directly or secondarily liable for infringement
* under applicable copyright law, except executing it on a computer or modifying a private copy. Propagation includes copying, distribution
* (with or without modification), making available to the public, and in some countries other activities as well.
*
* To “convey” a work means any kind of propagation that enables other parties to make or receive copies. Mere interaction with a user through
* a computer network, with no transfer of a copy, is not conveying.
*
* An interactive user interface displays “Appropriate Legal Notices” to the extent that it includes a convenient and prominently visible
* feature that (1) displays an appropriate copyright notice, and (2) tells the user that there is no warranty for the work (except to the
* extent that warranties are provided), that licensees may convey the work under this License, and how to view a copy of this License. If the
* interface presents a list of user commands or options, such as a menu, a prominent item in the list meets this criterion.
*
* Read more: https://www.gnu.org/licenses/gpl-3.0.en.html
*
* If you are a developer/designer please contact us hello@deco.agency about license transfer options.
*/

defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

define( 'DECOM_FILE', __FILE__ );
define( 'DECOM_PREFIX', 'decom_' );
define( 'DECOM_SITE_URL', 'https://decomments.com' );
define( 'DECOM_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'DECOM_PLUGIN_FOLDER', basename( DECOM_PLUGIN_PATH ) );
define( 'DECOM_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'DECOM_LANG_DOMAIN', 'decomments' );
define( 'DECOM_PLUGIN_NAME', DECOM_PLUGIN_FOLDER . '/' . basename( __FILE__ ) );
define( 'DECOM_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'DECOM_ASSETS_URL', DECOM_PLUGIN_URL . '/assets' );
define( 'DECOM_LIBRARIES_URL', DECOM_PLUGIN_URL . '/libraries' );
define( 'DECOM_COMPONENTS_URL', DECOM_PLUGIN_URL . '/components' );
define( 'DECOM_COMPONENTS_IMG', DECOM_COMPONENTS_URL . '/comments/assets/images' );
define( 'DECOM_CORE_PATH', DECOM_PLUGIN_PATH . '/core' );
define( 'DECOM_COMPONENTS_PATH', DECOM_PLUGIN_PATH . '/components' );
define( 'DECOM_LIBRARIES_PATH', DECOM_PLUGIN_PATH . '/libraries' );
define( 'DECOM_ASSETS_PATH', DECOM_PLUGIN_PATH . '/assets' );
define( 'DECOM_IMAGES_URL', DECOM_PLUGIN_URL . '/assets/images' );
define( 'DECOM_VIEWS_PATH', DECOM_PLUGIN_PATH . '/views' );
define( 'DECOM_TEMPLATES_PATH', DECOM_PLUGIN_PATH . '/views/templates' );
define( 'DECOM_CLASSES_PATH', DECOM_PLUGIN_PATH . '/classes' );
define( 'DECOM_TABLES_PATH', DECOM_PLUGIN_PATH . '/tables' );
define( 'DECOM_MODELS_PATH', DECOM_PLUGIN_PATH . '/models' );
define( 'DECOM_TEMPLATE_PATH', DECOM_PLUGIN_PATH . '/templates' );
define( 'DECOM_TEMPLATE_PATH_DEFAULT', DECOM_PLUGIN_PATH . '/templates/decomments/' );
define( 'DECOM_TEMPLATE_URL_DEFAULT', DECOM_PLUGIN_URL . '/templates/decomments/' );
define( 'DECOM_CUSTOM_TEMPLATE_PATH', get_stylesheet_directory() . '/decomments/' );
define( 'DECOM_CUSTOM_TEMPLATE_URL', get_stylesheet_directory_uri() . '/decomments/' );
require_once DECOM_PLUGIN_PATH . '/core/loader.php';
require_once DECOM_LIBRARIES_PATH . '/ajax/class-decom-ajax.php';
require_once DECOM_PLUGIN_PATH . '/admin/modules/decom-updater/init.php';
require_once DECOM_PLUGIN_PATH . '/components/notification-messages/class-decom-notification-message.php';
require_once DECOM_PLUGIN_PATH . '/admin/init.php';
$decom_hooks_handler   = DECOM_Loader::getHooksHandler();
$decom_error_reporting = error_reporting( 0 );

/**
 * Include scripts on admin panel initialization
 */
add_action( 'admin_init', array( $decom_hooks_handler, 'onAdminInit' ) );

/**
 * Add plugin menu items
 */

add_action( 'admin_menu', array( $decom_hooks_handler, 'registerMenuItems' ) );
if ( is_multisite() ) {
	add_action( 'network_admin_menu', array( $decom_hooks_handler, 'registerMenuItems' ) );
}

/**
 * On add/delete blog
 */
add_action( 'wpmu_new_blog', array( $decom_hooks_handler, 'onWpmuNewBlog' ), 10, 6 );

add_action( 'delete_blog', array( $decom_hooks_handler, 'onWpmuDeleteBlog' ), 5, 2 );

/**
 * Initialize plugin localization
 */
add_action( 'plugins_loaded', 'decom_load_textdomain' );
function decom_load_textdomain() {
	load_plugin_textdomain( 'decomments', false, DECOM_PLUGIN_FOLDER . '/languages' );
}


/**
 * On Plugin activation
 */
register_activation_hook( __FILE__, array( $decom_hooks_handler, 'onActivation' ) );

/**
 * On Plugin deactivation
 */
register_deactivation_hook( __FILE__, array( $decom_hooks_handler, 'onDeactivation' ) );
add_filter( 'decomments_comment_text', array( $decom_hooks_handler, 'onInsertMedia' ), 99 );
add_filter( 'avatar_defaults', array( $decom_hooks_handler, 'onWPDefaultAvatars' ) );
add_action( 'admin_enqueue_scripts', array( $decom_hooks_handler, 'onAdminEnqueueScripts' ) );
add_action( 'admin_print_scripts', array( $decom_hooks_handler, 'onAdminPrintScripts' ) );
add_action( 'wp_ajax_decom_edit_settings', array( $decom_hooks_handler, 'onWpAjaxDecomEditSettings' ) );
add_action( 'wp_ajax_decom_badges', array( $decom_hooks_handler, 'onWpAjaxDecomBadges' ) );
add_action( 'wp_ajax_decom_comments', array( $decom_hooks_handler, 'onWpAjaxDecomComments' ) );
add_action( 'wp_ajax_nopriv_decom_comments', array( $decom_hooks_handler, 'onWpAjaxDecomComments' ) );
add_filter( 'set_comment_cookies', array( $decom_hooks_handler, 'onSetCommentCookies' ) );
