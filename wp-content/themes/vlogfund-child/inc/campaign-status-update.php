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
		include_once( get_theme_file_path('/inc/emails/campaign-status/draft.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '5' /*&& $status !== 'draft'*/ ) : //Pending
		$sub_body['subject'] = 'Your campaign %%POST_TITLE%% is pending';
		ob_start();
		include_once( get_theme_file_path('/inc/emails/campaign-status/pending.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '3' ) : //Declined
		$sub_body['subject'] = 'We are sorry. Your campaign %%POST_TITLE%% was not approved';
		ob_start();
		include_once( get_theme_file_path('/inc/emails/campaign-status/declined.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '1' ) : //Vote
		$sub_body['subject'] = 'Your campaign %%POST_TITLE%% has been published';
		ob_start();
		include_once( get_theme_file_path('/inc/emails/campaign-status/vote.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '2' ) : //Contribute
		$sub_body['subject'] = 'Your campaign %%POST_TITLE%% hit a new stage';
		ob_start();
		include_once( get_theme_file_path('/inc/emails/campaign-status/contribute.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == '4' ) : //Success
		$sub_body['subject'] = 'Hurray! Your campaign %%POST_TITLE%% was successful';
		ob_start();
		include_once( get_theme_file_path('/inc/emails/campaign-status/success.php') );
		$sub_body['body'] = ob_get_contents();
		ob_get_clean();
	elseif( $status == 'vote-contribute' ) : //Vote to Contribute to User Email
		$sub_body['subject'] = '%%POST_TITLE%% now open for contributions';
		ob_start();
		include_once( get_theme_file_path('/inc/emails/campaign-status/user-vote-contribute.php') );
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
		$_note = '<p style="font-size: 18px !important; text-align: center;"><span style="font-family:open sans,helvetica neue,helvetica,arial,sans-serif">'.nl2br( stripslashes_deep( $_POST['_campaign_status_note'] ) ).'</span></p>';
	endif; //Endif

	//Check Old Status and New Status
	if( $new_status !== $old_status ) :

		if( $sub_body = vlogfund_post_status_get_email_subject_body( $new_status ) ) :
			$total_sales = vlogfund_get_product_sales($post_id);
			$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%', '_total_product_sales');
			$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url(), $_note, wc_price($total_sales));
			$email_subject = str_replace( $find_vars, $replace_vars, $sub_body['subject'] );
			$email_body = str_replace( $find_vars, $replace_vars, $sub_body['body'] );
			add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
			//Email to Author
			wp_mail( $author->user_email, htmlspecialchars_decode( $email_subject ), $email_body );
		endif; //Endif

		//Old Vote and New Status Contribute
		if( $old_status == '2' && $new_status == '3' ) :
			$get_voted_users = get_users( array( 'meta_key' => '_upvote_for_'.$post_id, 'meta_value' => 1 ) );
			if( !empty( $get_voted_users ) && ( $sub_body = vlogfund_post_status_get_email_subject_body( 'vote-contribute' ) ) ) : //Find Users who voted for this post
				$total_sales = vlogfund_get_product_sales($post_id);
				$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%', '_total_product_sales');
				$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url(), $_note, wc_price($total_sales) );
				$email_subject = str_replace( $find_vars, $replace_vars, $sub_body['subject'] );
				$email_body = str_replace( $find_vars, $replace_vars, $sub_body['body'] );
				add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
				foreach( $get_voted_users as $user ) : //Loop to Send Email
					//Email to Author
					wp_mail( $user->user_email, htmlspecialchars_decode($email_subject), $email_body );
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
		wp_mail( $author->user_email, htmlspecialchars_decode( $email_subject ), $email_body );
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
    if( $form_data['id'] == 98 || $form_data['id'] == 216 || $form_data['id'] == 64104 ) :
        if( isset( $_POST['post_status'] ) ) :
			$post = get_post( $post_id );
			$author = get_userdata( $post->post_author );
			if( $form_data['id'] == 64104 ) :
				$post_status = 5;
			else : //Else 
				$post_status = ( $_POST['post_status'] == 'pending' ) ? 5 : 6;
			endif; //Endif
            update_post_meta($post_id, 'wpcf-campaign-status', $post_status);			
			if( $sub_body = vlogfund_post_status_get_email_subject_body( $post_status ) ) :
				$total_sales = vlogfund_get_product_sales($post_id);
				$find_vars = array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%', '_total_product_sales' );
				$replace_vars = array( get_the_title( $post_id ), get_permalink( $post_id ), $post_id, home_url(), '', wc_price($total_sales) );
				$email_subject = str_replace( $find_vars, $replace_vars, $sub_body['subject'] );
				$email_body = str_replace( $find_vars, $replace_vars, $sub_body['body'] );
				add_filter( 'wp_mail_content_type', function(){	return "text/html";	} );
				//Email to Author
				wp_mail( $author->user_email, htmlspecialchars_decode($email_subject), $email_body );
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
		echo '<option value="customer">Note to creator</option>';
		echo '</select>';
	echo '</p>';
	echo '</div>';
}
endif;
if( !function_exists('vlogfund_update_org_to_cam_relationship') ) :
/**
 * Update Organization to Campaign while Edit Campaign
 **/
function vlogfund_update_org_to_cam_relationship($post_id, $form_data){

	//Edit Form Update Organization to Connect with Campaign
	if( $form_data['id'] == 216 && isset( $_POST['connect-organization-to-campaign'] ) && !empty( $_POST['connect-organization-to-campaign'] ) ) :
		$org_id = toolset_get_related_post($post_id,'organization-campaign','parent'); //Get Related Post
		toolset_disconnect_posts('organization-campaign', $org_id, $post_id); //Disconnect Related Post
		toolset_connect_posts( 'organization-campaign', $_POST['connect-organization-to-campaign'], $post_id ); //Update Related Post
	endif;
}
add_action('cred_save_data', 'vlogfund_update_org_to_cam_relationship', 10, 2);
endif;
if( !function_exists('vlogfund_send_email_draft_campaign_inactivity') ) :
/**
 * Send Email When Campaign in Inactivity Mode since 24/48/96 hours
 **/
function vlogfund_send_email_draft_campaign_inactivity(){

	//Ignore Staging to Run Cron
	if( strpos($_SERVER['SERVER_NAME'], 'vlogfundstage.wpengine.com') !== false ) :
		return;
	endif; //Endif	

	$considered_camps = array();
	$today = current_time('Y-m-d');
	$args  = array('post_type' => 'product', 'post_status' => 'draft', 'posts_per_page' => -1, 'orderby' => 'modified', 'order' => 'DESC');

	$subject = 'Your campaign %%POST_TITLE%% is still saved as a draft';
	ob_start();
	include_once( get_theme_file_path('/inc/emails/campaign-status/draft-inactivity.php') );
	$email_body = ob_get_contents();
	ob_get_clean();
	add_filter('wp_mail_content_type', function(){ return "text/html"; });
	
	//Last 96 Hours
	$last_96 = strtotime('-96 hours', strtotime($today));	
	$args['date_query'] = array( array('column' => 'post_modified', 'year' => date('Y',$last_96), 'month' => date('m',$last_96), 'day' => date('d',$last_96), 'inclusive' => true) );	
	$inactive_96hours 	= new WP_Query( $args );	
	//Check Inactive Campaigns in Last 96 Hours
	if( $inactive_96hours->have_posts() ) :
		while( $inactive_96hours->have_posts() ) : $inactive_96hours->the_post();
			//Email to Author
			$author_email 	= get_the_author_meta('user_email', $post->post_author);
			$find_vars 		= array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%');
			$replace_vars 	= array( get_the_title(), get_permalink(), get_the_ID(), home_url(), '' );
			$email_subject 	= str_replace($find_vars, $replace_vars, $subject);
			$email_body 	= str_replace($find_vars, $replace_vars, $email_body);
			wp_mail( $author_email, htmlspecialchars_decode( $email_subject ), $email_body );
			array_push($considered_camps, get_the_ID());
		endwhile; //Endwhile
	endif;//Endif
	wp_reset_postdata(); //Reset

	//Last 48 Hours
	$last_48 = strtotime('-48 hours', strtotime($today));
	$args['date_query'] = array( array('column' => 'post_modified', 'year' => date('Y',$last_48), 'month' => date('m',$last_48), 'day' => date('d',$last_48), 'inclusive' => true) );
	$args['post__not_in'] = $considered_camps;
	$inactive_48hours = new WP_Query( $args );	
	//Check Inactive Campaigns in Last 48 Hours
	if( $inactive_48hours->have_posts() ) :
		while( $inactive_48hours->have_posts() ) : $inactive_48hours->the_post();
			//Email to Author
			$author_email 	= get_the_author_meta('user_email', $post->post_author);
			$find_vars 		= array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%');
			$replace_vars 	= array( get_the_title(), get_permalink(), get_the_ID(), home_url(), '' );
			$email_subject 	= str_replace($find_vars, $replace_vars, $subject);
			$email_body 	= str_replace($find_vars, $replace_vars, $email_body);
			wp_mail( $author_email, htmlspecialchars_decode( $email_subject ), $email_body );
			array_push($considered_camps, get_the_ID());
		endwhile; //Endwhile
	endif;//Endif
	wp_reset_postdata(); //Reset
	
	//Last 24 Hours
	$last_24 = strtotime('-24 hours', strtotime($today));
	$args['date_query'] = array( array('column' => 'post_modified', 'year' => date('Y',$last_24), 'month' => date('m',$last_24), 'day' => date('d',$last_24), 'inclusive' => true) );
	$args['post__not_in'] = $considered_camps;
	$inactive_24hours = new WP_Query( $args );	
	//Check Inactive Campaigns in Last 24 Hours
	if( $inactive_24hours->have_posts() ) :
		while( $inactive_24hours->have_posts() ) : $inactive_24hours->the_post();
			//Email to Author
			$author_email 	= get_the_author_meta('user_email', $post->post_author);
			$find_vars 		= array( '%%POST_TITLE%%', '%%POST_LINK%%', '%%POST_ID%%', '%%HOME_URL%%', '%%STATUS_NOTE%%');
			$replace_vars 	= array( get_the_title(), get_permalink(), get_the_ID(), home_url(), '' );
			$email_subject 	= str_replace($find_vars, $replace_vars, $subject);
			$email_body 	= str_replace($find_vars, $replace_vars, $email_body);
			wp_mail( $author_email, htmlspecialchars_decode( $email_subject ), $email_body );
			//array_push($considered_camps, get_the_ID());
		endwhile; //Endwhile
	endif;//Endif
	wp_reset_postdata(); //Reset
}
//add_action('init', 'vlogfund_send_email_draft_campaign_inactivity');
add_action('vlogfund_draft_campaign_inactivity', 'vlogfund_send_email_draft_campaign_inactivity');
endif;
if( !function_exists('vlogfund_schedule_draft_campaign_cron_jobs') ) :
/**
 * Send Email When Campaign in Inactivity Mode since 24/48/96 hours
 **/
function vlogfund_schedule_draft_campaign_cron_jobs(){
	if( !wp_next_scheduled('vlogfund_draft_campaign_inactivity') ) :
		$ve = get_option('gmt_offset' ) > 0 ? '-' : '+';
		wp_schedule_event(strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' ), 'daily', 'vlogfund_draft_campaign_inactivity');
    endif; //Endif
}
add_action('wp', 'vlogfund_schedule_draft_campaign_cron_jobs');
endif;