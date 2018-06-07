<?php
/**
 * Email Notification When Post Status Update
 *
 * Handles to send email notification when post status updated by admin
 **/
if( !function_exists('vlogfund_register_post_statuses') ) :
/**
 * Register Post Status
 **/
function vlogfund_register_post_statuses(){
	$new_status = array( 'declined' => _x( 'Declined', 'product' ),
					'vote' => _x( 'Vote', 'product' ),
					'contribute' => _x( 'Contribute', 'product' ),
					'success' => _x('Success', 'product' ) );
	foreach( $new_status as $status => $status_name ) :
		register_post_status( $status, array(
			'label'	=> $status_name,
			'public'=> false,
			'exclude_from_search'=> true,
			'show_in_admin_all_list'=> true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( $status_name . ' <span class="count">(%s)</span>', $status_name . ' <span class="count">(%s)</span>' ),
		) );
	endforeach;
}
//add_action( 'init', 'vlogfund_register_post_statuses', 0 );
endif;
if( !function_exists('vlogfund_register_post_statuses_dropdown') ) :
/**
 * Add Statuses to Dropdown
 **/
function vlogfund_register_post_statuses_dropdown(){
	global $post;
	$new_status = array( 'declined' => __('Declined'),
					'vote' => __('Vote' ),
					'contribute' => __('Contribute'),
					'success' => __('Success') );

	if( get_post_type( $post ) == 'product' ) : //Product ?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				<?php if( isset( $new_status[$post->post_status] ) ) : //Check Status ?>
					$('#post-status-display').html( '<?php echo $new_status[$post->post_status]; ?>' );
				<?php endif; //Endif ?>
				<?php foreach( $new_status as $status => $status_name ) :
					$selected = ($post->post_status == $status) ? ' selected="selected"' : ''; ?>
					$('select#post_status').append('<option value="<?php echo $status;?>" <?php echo $selected;?>><?php echo $status_name;?></option>');
           		<?php endforeach; ?>
			});
        </script>
	<?php endif; //Endif

}
//add_action('admin_footer-post.php', 'vlogfund_register_post_statuses_dropdown');
//add_action('admin_footer-post-new.php', 'vlogfund_register_post_statuses_dropdown');
endif;
if( !function_exists('vlogfund_post_status_get_email_subject_body') ) :
/**
 * Post Status Email Body
 **/
function vlogfund_post_status_get_email_subject_body( $status ) {

	$sub_body = array( 'subject' => '', 'body' => '' );

	if( $status == '6' /*&& $status !== 'pending'*/ ) : //Draft
		$sub_body['subject'] = 'Your campaign %%POST_TITLE%% has been saved as a draft';
		ob_start();
		include_once( get_theme_file_path('/inc/campaign-status-emails/draft.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '5' /*&& $status !== 'draft'*/ ) : //Pending
		$sub_body['subject'] = 'Your campaign %%POST_TITLE%% is pending';
		ob_start();
		include_once( get_theme_file_path('/inc/campaign-status-emails/pending.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '3' ) : //Declined
		$sub_body['subject'] = 'We are sorry. Your campaign %%POST_TITLE%% was not approved';
		ob_start();
		include_once( get_theme_file_path('/inc/campaign-status-emails/declined.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '1' ) : //Vote
		$sub_body['subject'] = 'Your campaign %%POST_TITLE%% has been published';
		ob_start();
		include_once( get_theme_file_path('/inc/campaign-status-emails/vote.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '2' ) : //Contribute
		$sub_body['subject'] = 'Your campaign %%POST_TITLE%% has been published';
		ob_start();
		include_once( get_theme_file_path('/inc/campaign-status-emails/contribute.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '4' ) : //Success
		$sub_body['subject'] = 'Hurray! Your campaign %%POST_TITLE%% was successful';
		ob_start();
		include_once( get_theme_file_path('/inc/campaign-status-emails/success.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == 'vote-contribute' ) : //Vote to Contribute to User Email
		$sub_body['subject'] = '%%POST_TITLE%% now open for contributions';
		ob_start();
		include_once( get_theme_file_path('/inc/campaign-status-emails/user-vote-contribute.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	endif;
	return $sub_body;

}
endif;
if( !function_exists('vlogfund_post_status_update') ) :
/**
 * Save Post Update
 **/
function vlogfund_post_status_update( $post_id, $post ){

	$post_type = get_post_type( $post_id ); //Post Type
	$author = get_userdata( $post->post_author );
	$new_status = isset( $_POST['wpcf']['campaign-status'] ) ? $_POST['wpcf']['campaign-status'] : '';
	$old_status = get_post_meta($post_id, 'wpcf-campaign-status', true);
	//If this is just a revision, don't send the email.
	if( wp_is_post_revision( $post_id ) && 'product' !== $post_type ) :
		return $post_id;
	endif;

	//Check Old Status and New Status
	if( $new_status !== $old_status ) :

		if( $sub_body = vlogfund_post_status_get_email_subject_body( $new_status ) ) :
			$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%');
			$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url() );
			$email_subject = str_replace( $find_vars, $replace_vars, $sub_body['subject'] );
			$email_body = str_replace( $find_vars, $replace_vars, $sub_body['body'] );
			add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
			//Email to Author
			wp_mail( $author->user_email, $email_subject, $email_body );
		endif; //Endif

		//Old Vote and New Status Contribute
		if( $old_status == '2' && $new_status == '3' ) :
			$get_voted_users = get_users( array( 'meta_key' => '_upvote_for_'.$post_id, 'meta_value' => 1 ) );
			if( !empty( $get_voted_users ) && ( $sub_body = vlogfund_post_status_get_email_subject_body( 'vote-contribute' ) ) ) : //Find Users who voted for this post
				$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%');
				$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url() );
				$email_subject = str_replace( $find_vars, $replace_vars, $sub_body['subject'] );
				$email_body = str_replace( $find_vars, $replace_vars, $sub_body['body'] );
				add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
				foreach( $get_voted_users as $user ) : //Loop to Send Email
					//Email to Author
					wp_mail( $user->user_email, $email_subject, $email_body );
				endforeach;
			endif; //Endif
		endif; //Endif

	endif; //Endif

}
add_action('save_post', 'vlogfund_post_status_update', 10, 2);
//add_action('publish_post', 'vlogfund_post_status_update', 10, 2 );
endif;
if( !function_exists('vlogfund_post_inserted_update') ) :
/**
 * Insert Post Update
 **/
function vlogfund_post_inserted_update( $post_id, $post ){

	$post_type = get_post_type( $post_id ); //Post Type
	$author = get_userdata( $post->post_author );

	//If this is just a revision, don't send the email.
	if( wp_is_post_revision( $post_id ) && 'product' !== $post_type ) :
		return $post_id;
	endif;

	if( $sub_body = vlogfund_post_status_get_email_subject_body( $post->post_status ) ) :
		$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%');
		$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url() );
		$email_subject = str_replace( $find_vars, $replace_vars, $sub_body['subject'] );
		$email_body = str_replace( $find_vars, $replace_vars, $sub_body['body'] );
		add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
		//Email to Author
		wp_mail( $author->user_email, $email_subject, $email_body );
	endif; //Endif
}
//add_action('wp_insert_post', 'vlogfund_post_inserted_update', 10, 2 );
endif;

if( !function_exists('vlogfund_post_cred_save_data') ) :
function vlogfund_post_cred_save_data($post_id, $form_data){

	// if a specific form
    if( $form_data['id'] == 98 || $form_data['id'] == 216 ) :
        if( isset( $_POST['post_status'] ) ) :
			$post = get_post( $post_id );
			$author = get_userdata( $post->post_author );
			$post_status = ( $_POST['post_status'] == 'pending' ) ? 5 : 6;
            update_post_meta($post_id, 'wpcf-campaign-status', $post_status);
			if( $sub_body = vlogfund_post_status_get_email_subject_body( $post_status ) ) :
				$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%');
				$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url() );
				$email_subject = str_replace( $find_vars, $replace_vars, $sub_body['subject'] );
				$email_body = str_replace( $find_vars, $replace_vars, $sub_body['body'] );
				add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
				//Email to Author
				wp_mail( $author->user_email, $email_subject, $email_body );
			endif; //Endif
        endif; //Endif
    endif; //Endif
}
add_action('cred_save_data', 'vlogfund_post_cred_save_data',100,2);
endif;
