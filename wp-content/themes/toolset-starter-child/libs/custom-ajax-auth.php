<?php
/*
http://fellowtuts.com/wordpress/wordpress-ajax-login-and-register-without-a-plugin/
*/
function ajax_auth_init(){

  	wp_register_script('validate-script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js' );
    wp_enqueue_script('validate-script');

    //wp_register_script('ajax-auth-script', get_template_directory_uri() . '/js/ajax-auth-script.js', array('jquery') );
    //wp_enqueue_script('ajax-auth-script');+

    wp_enqueue_script('js', get_theme_file_uri('js.js'), array('jquery'), null, true );

    wp_localize_script( 'js', 'ajax_auth_object', array(
        'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http') ),
        'redirecturl' => get_permalink(), //$_SERVER[‘REQUEST_URI’], //home_url() also works //home_url(add_query_arg(array(),$wp->request)),
        'loadingmessage' => __('Sending user info, please wait...')
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
	// Enable the user with no privileges to run ajax_register() in AJAX
	add_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );
}

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'ajax_auth_init');
}

function ajax_login(){

    // First check the nonce, if it fails the function will break
    //check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
  	$info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;
	$user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
		echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
    } else {
		wp_set_current_user($user_signon->ID);
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
    }
    die();
}



function ajax_register(){

    // First check the nonce, if it fails the function will break
    //check_ajax_referer( 'ajax-register-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
  	$info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['user_login'] = sanitize_user($_POST['username']) ;
    $info['user_pass'] = sanitize_text_field($_POST['password']);
	$info['user_email'] = sanitize_email( $_POST['email']);
	// Register the user
    $user_register = wp_insert_user( $info );
 	if ( is_wp_error($user_register) ){
		$error  = $user_register->get_error_codes()	;
		if(in_array('empty_user_login', $error))
			echo json_encode(array('registerin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'))));
		elseif(in_array('existing_user_login',$error))
			echo json_encode(array('registerin'=>false, 'message'=>__('This username is already registered.')));
		elseif(in_array('existing_user_email',$error))
        	echo json_encode(array('registerin'=>false, 'message'=>__('This email address is already registered.')));
    } else {
		include_once( get_theme_file_path('/inc/mailchimp/mailchimp.php') );
		$MailChimp = new MailChimp( VLOG_MAILCHIMP_API ); //Check Success
		$result = $MailChimp->post('lists/'.VLOG_MAILCHIMP_LIST.'/members', array(
			'email_address' => $info['user_email'],
			'merge_fields' => array( 'SIGNUPPAGE' => $_POST['source'] ),
			'status' => 'subscribed'
		));
		//Make Logged in To User
		$singon = array();
		$singon['user_login'] = $info['nickname'];
		$singon['user_password'] = $info['user_pass'];
		$singon['remember'] = true;
		$user_signon = wp_signon( $singon, false );
		if( is_wp_error( $user_signon ) ){
			echo json_encode(array('registerin'=>false, 'message'=>__('Wrong username or password.')));
		} else {
			wp_set_current_user($user_signon->ID);
			echo json_encode(array('registerin'=>true, 'message'=>__('Registration successful, redirecting...')));
		}
    }

    die();
}
