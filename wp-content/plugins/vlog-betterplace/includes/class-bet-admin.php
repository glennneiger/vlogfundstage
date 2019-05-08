<?php
/**
 * Admin Class
 *
 * Handles all admin functions
 *
 * @since Vlog Betterplace 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('Vlogbet_Admin') ) :

class Vlogbet_Admin{
	
	//Construct which run class
	public function __construct(){		
		//Admin Menu
		add_action( 'admin_menu',	array( $this, 'register_sub_menu' ) );
		//Enqueue Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		//Admin AJAX
		add_action( 'wp_ajax_vlog_betterplace_orgs_import_update', array( $this, 'vlog_betterplace_orgs_import_update' ) );
	}
	/**
     * Register submenu
	 *
     * @since Vlog Betterplace 1.0
     **/
    public function register_sub_menu() {
		//Import Betterplace Records
		add_submenu_page( 
            'edit.php?post_type=organizations', 
			__('Import / Update Betterplace Organizations','vlog-betterplace'), 
			__('Betterplace','vlog-betterplace'),
			'manage_options',
			'betterplace-import-update',
			array( $this, 'update_import_betterplace_callback' )
        );		
    }	
	/**
	 * Enqueue All Scripts / Styles
	 *
	 * @since YouTube Channels 1.0
	 **/
	public function register_scripts( $hook ){
		if( $hook == 'organizations_page_betterplace-import-update' ) : 
			//Script for Admin Function
			wp_enqueue_script( 'vlogbet-admin-script', 	VLOGBET_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), null, true );
			wp_localize_script( 'vlogbet-admin-script', 'Vlogbet_Admin_Obj', array( 'secure'  => wp_create_nonce( 'vlogbet-secure-code' ) ) );
		endif; //Endif
	}
	/**
	 * Get All Betterplace Organizations
	 *
	 * Handles to get all betterplace organizations
	 *
	 * @since Vlog Betterplace 1.0
	 **/
	public function get_betterplace_organizations( $args = array() ){
		$args['page']		= ( isset( $args['page'] ) && !empty( $args['page'] ) ) 		? $args['page'] 	: 1;
		$args['per_page'] 	= ( isset( $args['per_page'] ) && !empty( $args['per_page'] ) ) ? $args['per_page'] : 100;
		$final_api_url 		= add_query_arg( $args, VLOGBET_BETTERPLACE_ORG_API_URL );		
		// Try to retrieve saved data from the cache
		$org_data = get_transient('betterplace_orgs_page_'.$args['page']);
		if( $org_data === false ) :
			$response = wp_remote_get($final_api_url);
			if ( is_array( $response ) && !is_wp_error( $response ) ) :
				// Save the data in the cache, let it live for up to 1 hour (3600 seconds)
        		set_transient('betterplace_orgs_page_'.$args['page'], $response['body'], (60 * 60 * 24));
				return json_decode( $response['body'] ); // use the content				
			else :
				return false; //Return Nothing
			endif;
		else : //Else return cached objects
			return json_decode( $org_data );
		endif; //Endif
	}
	/**
	 * Referred Upvotes Submenu Page
	 *
	 * @since Vlog Betterplace 1.0
	 **/
	public function update_import_betterplace_callback(){
		global $wpdb; 
		$total_orgs = $this->get_betterplace_organizations(); 
		//echo '<pre>';print_r($total_orgs); echo '</pre>';
		$total 		= !empty( $total_orgs->total_entries ) ? $total_orgs->total_entries : 0; ?>
        <style type="text/css">
			.update-betterplace-orgs-progress-wrap{ width: 100%; background-color: #d3d3d3; margin-bottom:15px; display:none; }
			.update-betterplace-orgs-progress{ width:0%; height:30px; background-color: green; }
			.betterplace-result-count, .betterplace-imported, .betterplace-updated{ margin-bottom:10px; display:none; }
			.update-betterplace-orgs-results{ margin-top:20px; overflow:auto; width:100%; max-height:450px; }
			.update-betterplace-orgs-results span{ display:block; }
			.betterplace-wrap .count, .betterplace-result-count span{ font-weight:bold; }
		</style>
		<div class="wrap betterplace-wrap">
			<h2><?php _e('Import/Update Betterplace Organizations','vlog-betterplace');?></h2>
			<p><strong><?php _e('Update may take some time. Please do not close your browser or refresh the page until the process is complete.', 'vlog-betterplace');?></strong></p>
            <div class="update-betterplace-orgs-progress-wrap"><div class="update-betterplace-orgs-progress"></div></div>
            <div class="betterplace-result-count"><?php printf('%1$s <span class="updated">%2$s</span> %3$s <span class="total">%4$s</span></div>', __('Updated / Imported', 'vlog-betterplace'), 0, __('out of'), $total);?>
            <div class="betterplace-imported"><?php printf('<span class="count">%1$s</span> %2$s', 0, __('Organizations Imported.', 'vlog-betterplace'));?></div>
            <div class="betterplace-updated"><?php printf('<span class="count">%1$s</span> %2$s', 0, __('Organizations Updated.', 'vlog-betterplace'));?></div>
            <a href="#" class="button button-primary import-update-betterplace-orgs"><?php _e('Import / Update','vlog-betterplace');?></a>
            <div class="update-betterplace-orgs-results"></div>
		</div>
		<?php
	}
	/**
	 * Import/Update Betterplace Records
	 **/
	public function vlog_betterplace_orgs_import_update(){
		global $wpdb;
		$updated_msg = $response = array();
		if( ! check_ajax_referer( 'vlogbet-secure-code', 'secure' ) ) :
			wp_send_json_error( 'Invalid security token.' );
			wp_die(); //To Proper Output
		else : //Else Process Update
			$page 		= ( isset( $_POST['page'] ) 	&& !empty( $_POST['page'] ) ) 		? intval( $_POST['page'] ) 		: 1;				
			$updated	= ( isset( $_POST['updated'] ) 	&& !empty( $_POST['updated'] ) ) 	? intval( $_POST['updated'] ) 	: 0;
			$imported	= ( isset( $_POST['imported'] ) && !empty( $_POST['imported'] ) ) 	? intval( $_POST['imported'] ) 	: 0;			
			$in_middle	= false;
			if( $page <= 1 && get_option('_betterplace_orgs_last_updated') ) : //Check Last Updated and Page Empty
				$page = get_option('_betterplace_orgs_last_updated');
				$in_middle = true;
			endif; //Endif
			$betterplace_orgs = $this->get_betterplace_organizations( array('page' => $page) );
			if( $in_middle && get_option('_betterplace_orgs_last_updated') ) : //Check Last Updated and Page Empty
				$updated = $betterplace_orgs->offset; //Set Updated Records
			endif;
			$counter 	= ( !empty( $imported ) || !empty( $updated ) ) ? ( $updated + $imported ) : 0;
			if( !empty( $betterplace_orgs->data ) ) : //Check Organizations Not Empty			
				foreach( $betterplace_orgs->data as $org ) :
					$betterplace_org_id = $org->id;
					$exist = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE 1=1 AND meta_key='_org_betterplace_id' AND meta_value='$betterplace_org_id';" );
					$org_args = array( 'post_title' => $org->name, 'post_content' => $org->description, 'post_type' => 'organizations', 'post_status' => 'publish' );
					if( !empty( $exist ) ) : //To be updated											
						$org_args['ID'] = $exist;
						$vlog_org_id = wp_update_post( $org_args );
						if( $vlog_org_id ) :
							$updated_msg[] = '<span><strong>'.$org->name.'</strong> Updated. ' . date('H:i:s').'</span>';
							$counter++; //Increase Counter
							$updated++;
							//Betterplace ID
							update_post_meta($vlog_org_id, '_org_betterplace_id', $betterplace_org_id);
						endif; //Endif
					else : //To be imported
						$vlog_org_id = wp_insert_post( $org_args );
						if( $vlog_org_id ) :
							$updated_msg[] = '<span><strong>'.$org->name.'</strong> Imported. ' . date('H:i:s').'</span>';
							$counter++; //Increase Counter
							$imported++;
							//Betterplace ID
							update_post_meta($vlog_org_id, '_org_betterplace_id', $betterplace_org_id);
						endif; //Endif
					endif;
					//If Failed
					if( empty( $vlog_org_id ) ) :
						$updated_msg[] = '<span><strong>'.$org->name.'</strong> Failed.</span>';
					else : //Update Meta Values
						//Update Rest of The Data				
						if( isset( $org->links[3] ) && !empty( $org->links[3] ) && $org->links[3]->rel == 'website' ) : //Website Link
							update_post_meta($vlog_org_id, 'wpcf-organization-website', $org->links[3]->href);
						endif; //Endif
						if( !empty( $org->zip ) ) : //Zipcode
							update_post_meta($vlog_org_id, 'wpcf-organization-zip', 	$org->zip);
						endif; //Endif
						if( !empty( $org->tax_deductible ) ) : //Tax Deductible
							update_post_meta($vlog_org_id, 'wpcf-organization-tax_deductible', 1);
						endif; //Endif
						$location = array();
						if( !empty( $org->street ) ) : //Street
							update_post_meta($vlog_org_id, 'wpcf-organization-street', 	$org->street);
							$location[] = $org->street;
						endif; //Endif
						if( !empty( $org->city ) ) : 	//City
							update_post_meta($vlog_org_id, 'wpcf-organization-city', 	$org->city);
							$location[] = $org->city;
						endif; //Endif
						if( !empty( $org->country ) ) : //Country
							update_post_meta($vlog_org_id, 'wpcf-organization-country', $org->country);
							$location[] = $org->country;
						endif; //Endif
						if( !empty( $location ) ) : 	//Check Location
							$address = join(', ', $location);
							update_post_meta($vlog_org_id, 'wpcf-organization-address', $address);
						endif; //Endif
						if( !empty( $org->picture ) ) :
							foreach($org->picture->links as $img ) :
								if( $img->rel == 'fill_200x200' ) : //Check 200x200 Image
									update_post_meta($vlog_org_id, 'wpcf-organization-betterplace-image', $img->href );
									break;
								endif; //Endif
							endforeach; //Endforeach
						endif; //Endif
					endif;					
				endforeach;								
			endif; //Endif
			$next_page = intval( $page + 1 );			
			$response['updated_msg']= ( !empty( $updated_msg ) ? join('', $updated_msg) : '');
			$response['page'] 		= $next_page;
			$response['success'] 	= 1;
			$response['imported'] 	= $imported;
			$response['updated'] 	= $updated;			
			$response['total_entries'] 	= $betterplace_orgs->total_entries;
			//Cache Next Page Data
			$this->get_betterplace_organizations( array('page' => $next_page) );
			if( $counter >= $betterplace_orgs->total_entries ) :
				$response['all_updated'] = 1;
				//Track Last Updated Records
				//delete_option('_betterplace_orgs_last_updated');
			else : //Else
				//Track Last Updated Records
				update_option('_betterplace_orgs_last_updated', $next_page);
			endif; //Endif
		endif; //Endif
		wp_send_json( $response );
		wp_die();
	}
}
//Run Class
$vlogbet_admin = new Vlogbet_Admin();
endif;