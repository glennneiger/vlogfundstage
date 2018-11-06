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
	
	//Update the Status Notes
	if( isset( $_POST['_campaign_status_note'] ) && !empty( $_POST['_campaign_status_note'] ) ) :
		$current_user = wp_get_current_user();
		$saved_notes = get_post_meta($post->ID, '_campaign_status_notes', true) ? get_post_meta($post->ID, '_campaign_status_notes', true) : array();
		$new_note = array( 'content' => $_POST['_campaign_status_note'], 'date' => current_time('timestamp'), 'type' => $_POST['_campaign_status_note_type'], 'by_user' => $current_user->user_login );
		$saved_notes[] = $new_note;
		//Update Notes
		update_post_meta($post->ID, '_campaign_status_notes',$saved_notes);
	endif; //Endif

	$_note = '';
	if( isset( $_POST['_campaign_status_note'] ) && !empty( $_POST['_campaign_status_note'] ) && $_POST['_campaign_status_note_type'] == 'customer' ) :
		$_note = '<p class="text-center float-center" align="center" style="Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding:0;text-align:center">'.nl2br( stripslashes_deep( $_POST['_campaign_status_note'] ) ).'</p>';
	endif; //Endif

	//Check Old Status and New Status
	if( $new_status !== $old_status ) :

		if( $sub_body = vlogfund_post_status_get_email_subject_body( $new_status ) ) :
			$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%');
			$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url(), $_note );
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
				$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%');
				$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url(), $_note );
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
/**
 * Send Email to User When Submit Campaign
 **/
function vlogfund_post_cred_save_data($post_id, $form_data){

	// if a specific form
    if( $form_data['id'] == 98 || $form_data['id'] == 216 ) :
        if( isset( $_POST['post_status'] ) ) :
			$post = get_post( $post_id );
			$author = get_userdata( $post->post_author );
			$post_status = ( $_POST['post_status'] == 'pending' ) ? 5 : 6;
            update_post_meta($post_id, 'wpcf-campaign-status', $post_status);
			if( $sub_body = vlogfund_post_status_get_email_subject_body( $post_status ) ) :
				$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%');
				$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url(), '' );
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

if( !function_exists('vlogfund_campaign_notes_metabox') ) :
/**
 * Add Meta for Campaign Notes
 **/
function vlogfund_campaign_notes_metabox(){
	//Campaign Notes
    add_meta_box( 'campaign-notes', __( 'Campaign Notes' ), 'vlogfund_campaign_notes_callback', 'product', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'vlogfund_campaign_notes_metabox' );
endif;
if( !function_exists('vlogfund_campaign_notes_callback') ) :
/**
 * Add Meta for Campaign Notes Callback
 **/
function vlogfund_campaign_notes_callback( $post ){
	$saved_notes = get_post_meta($post->ID, '_campaign_status_notes', true);
	echo '<ul class="order_notes">';
	if( !empty( $saved_notes ) ) :
		foreach( array_reverse( $saved_notes ) as $note ) :
			if( isset( $note['content'] ) && !empty( $note['content'] ) ) :
				echo '<li class="note'.( ( $note['type'] == 'customer' ) ? ' customer-note' : '').'">';
					echo '<div class="note_content"><p>'.$note['content'].'</p></div>';
					echo '<p class="meta"><abbr class="exact-date">'.sprintf('%1$s %2$s %3$s %4$s', __('added on'), date('F j, Y', $note['date']), __('at'), date('H:i a', $note['date'])).'</abbr> '. sprintf('%1$s %2$s', __('by'), $note['by_user']).'</p>';
				echo '</li>';
			endif; //Endif
		endforeach; //Endforeach
	else : //Else
		echo '<li>There are no notes yet.</li>';
	endif;
	echo '</ul>';
	echo '<style type="text/css">';
	echo '.campaign_add_note #_campaign_status_note{ width: 100%; height: 50px; }';
	echo '.campaign_add_note{ border-top: 1px solid #ddd; }';
	echo '</style>';
	echo '<div class="campaign_add_note">';
	echo '<p>';
		echo '<label for="_campaign_status_note">Add note</label>';
		echo '<textarea type="text" name="_campaign_status_note" id="_campaign_status_note" class="input-text" cols="20" rows="5"></textarea>';
	echo '</p>';
	echo '<p>';
	echo '<label for="_campaign_status_note_type" class="screen-reader-text">Note type</label>';
	echo '<select name="_campaign_status_note_type" id="_campaign_status_note_type">';
		echo '<option value="private">Private note</option>';
		echo '<option value="customer">Note to customer</option>';
		echo '</select>';
	echo '</p>';
	echo '</div>';
}
endif;