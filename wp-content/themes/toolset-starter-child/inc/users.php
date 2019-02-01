<?php
/**
 * All Users Related Functions
 **/
if( !function_exists('vlog_user_last_login') ) :
//Record Users Last Login
function vlog_user_last_login( $user_login, $user ) {
	update_user_meta( $user->ID, 'last_login', time() );
}
add_action('wp_login', 'vlog_user_last_login', 10, 2);
endif; //Endif
if( !function_exists('vlog_user_custom_columns') ) :
//Show Users Last Login in Admin Column
function vlog_user_custom_columns( $column ) {
    $column['last_login'] = 'Last Login';
    return $column;
}
add_filter('manage_users_columns', 'vlog_user_custom_columns');
endif;
if( !function_exists('vlog_user_custom_columns_data') ) :
//Show Users Last Login in Admin Column
function vlog_user_custom_columns_data( $val, $column, $user_id ) {
    switch($column) :
        case 'last_login' :
			$last_login = get_user_meta( $user_id, 'last_login', time() );
            $val = !empty( $last_login )? date('d/m/Y', $last_login) : '&mdash;';
            break;
    endswitch;
    return $val;
}
add_filter('manage_users_custom_column', 'vlog_user_custom_columns_data', 10, 3);
endif;