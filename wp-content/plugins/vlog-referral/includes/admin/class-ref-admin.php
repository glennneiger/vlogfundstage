<?php
/**
 * Admin Class
 *
 * Handles all admin functions
 *
 * @since Vlog Referral 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( !class_exists('Vlogref_Admin') ) :

class Vlogref_Admin{
	
	//Construct which run class
	public function __construct(){		
		//Admin Menu
		add_action( 'admin_menu',	array( $this, 'register_sub_menu' ) );		
		//Winner Process
		add_action( 'admin_init',	array( $this, 'referral_campaign_winner_process' ) );
		//Add Winner Process Popup
		add_action( 'admin_footer',	array( $this, 'referral_campaign_winner_process_popup' ) );
		//Admin Scripts
		add_action( 'admin_enqueue_scripts',array( $this, 'register_admin_scripts' ) );		
		//Add Referred Upvote Column in Product
		add_filter( 'manage_product_posts_columns', array( $this, 'referral_product_admin_columns' ), 99 );
		//Add Referred Upvote Column in Product Table Data
		add_action( 'manage_product_posts_custom_column' , array( $this, 'referral_product_admin_columns_data' ), 99, 2 );
	}
	/**
     * Register submenu
	 *
     * @since Vlog Referral 1.0
     **/
    public function register_sub_menu() {
		add_submenu_page( 
            'edit.php?post_type=product', 
			__('Referred Upvotes','vlog-referral'), 
			__('Referred Upvotes', 'vlog-referral'),
			'manage_options',
			'referred-upvotes',
			array( $this, 'referred_upvotes_callback' )
        );
    }	
	/**
	 * Update Channels Submenu Page
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function referred_upvotes_callback(){
		
		global $wpdb;
		echo '<div class="wrap">';		
			if( isset( $_GET['campaign'] ) && !empty( $_GET['campaign'] ) ) : //Check Particular Campaign List
				include_once( VLOGREF_PLUGIN_PATH . '/includes/admin/class-list-campaign-upvotes.php');
			else : //Else 
				include_once( VLOGREF_PLUGIN_PATH . '/includes/admin/class-list-referred-upvotes.php');			
			endif; //Endif
		echo '</div>';
	}
	/**
	 * Campaign Winner Process POpup
	 *
	 * @since Vlog Referral 1.0
	 **/
	public function referral_campaign_winner_process_popup(){ 
		global $pagenow;
		if( isset( $_GET['page'] ) && $_GET['page'] == 'referred-upvotes' 
			&& isset( $_GET['campaign'] ) && !empty( $_GET['campaign'] ) ) : //Check Page
			$campaign = $_GET['campaign']; ?>
			<style type="text/css">
				.vf-ref-overlay{ position: fixed; top: 0; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.7); transition: opacity 500ms; visibility: hidden; opacity: 0; }
				.vf-ref-popup-wrapper{ margin: 70px auto; padding: 20px; background: #fff; border-radius: 5px; width: 45%; position: relative; transition: all 5s ease-in-out; }
				.vf-ref-popup-wrapper h2{ margin-top: 0; }
				.vf-ref-popup-wrapper .close{ position: absolute; top: 20px; right: 30px; transition: all 200ms; font-size: 30px; font-weight: bold; text-decoration: none; color: #333; }
				.vf-ref-popup-wrapper .close:hover{ color: #06D85F; }
				.vf-ref-popup-wrapper .content{ overflow: auto; }
				.vf-ref-popup-wrapper .spinner{ text-align: center; margin: 0 auto; float: none; display: block; }
				.vf-ref-popup-wrapper .content label{ padding:5px 0; display:block; }
				.vf-ref-popup-wrapper .content a{ margin-top:10px; }
				.vf-ref-popup-wrapper .username{ color:#0073aa; }
 			</style>
			<div id="winner-process" class="vf-ref-overlay">
				<div class="vf-ref-popup-wrapper">
					<h2 class="popup-title"><?php printf( '%1$s <span class="username"></span> %2$s <a href="%3$s" target="_blank">%4$s</a>', __('Make','vlog-referral'), __('winner for','vlog-referral'), get_permalink( $_GET['campaign'] ), get_the_title( $_GET['campaign'] ) );?></h2><a class="close" href="#">&times;</a>
					<div class="content">
						<?php $prizes = vlogref_referral_prizes($campaign);
							$given_prizes = vlogref_get_campaign_winners($campaign);
							if( !empty( $prizes ) ) : //Prizes
								echo '<h3>'.__('Choose Prize', 'vlog-referral').'</h3>';
								foreach( $prizes as $key => $prize ) :
									$checked = ( $key == 0 ) ? $prize : '';
									$prize_data = vlogref_get_prize_details( $prize );
									$already_won = '';
									if( in_array($prize,$given_prizes) ) :
										$won_userid = array_search($prize,$given_prizes);
										$userdata = get_userdata($won_userid);
										$already_won .= sprintf('<span class="won"> - %1$s <a href="%2$s" target="_blank"><strong>%3$s</strong></a></span>',__('won by','vlog-referral'), get_edit_user_link($userdata->ID), $userdata->user_login);
									endif; //Endif
									echo '<label for="winning_prize_'.$prize.'">';										
									echo '<input type="radio" name="winning_prize" id="winning_prize_'.$prize.'" value="'.$prize.'" '.checked($checked, $prize, false).'/>'.$prize_data['title'];
									echo $already_won;
									echo '</label>';
								endforeach; //Endforeach
								echo '<input type="hidden" name="winning_user" id="winning_user"/>';
								echo '<a class="button-primary declare-btn" href="'.add_query_arg( array( 'page' => 'referred-upvotes', 'campaign' => $_GET['campaign'] ), admin_url('edit.php?post_type=product') ).'">'.__('Make Winner!','vlog-referral').'</a>';
							else :
								echo '<p>'.__('There is no prizes setup for this campaign.','vlog-referral').'</p>';
							endif; //Endif 
						?>							
					</div><!--/.content-->
				</div><!--/.vf-ref-popup-wrapper-->
			</div><!--/.vf-ref-overlay-->

	<?php endif; //Endif
	}
	/**
     * Campaign Winner Process
	 *
     * @since Vlog Referral 1.0
     **/
    public function referral_campaign_winner_process() {
		
		if( isset( $_GET['campaign'] ) && !empty( $_GET['campaign'] ) 
			&& isset( $_GET['winner'] ) && !empty( $_GET['winner'] )
			&& isset( $_GET['prize'] ) && !empty( $_GET['prize'] ) ) :
			$campaign 	= $_GET['campaign']; //Campaign
			$user 		= $_GET['winner']; //Winner User
			$prize  	= $_GET['prize']; //Winning Prize
			$winners 	= vlogref_get_campaign_winners( $campaign );
			$user_won	= vlogref_get_user_won_campaigns( $user );
			if( !array_key_exists($user,$winners) ) : //Check user already exist or not
				$winners[$user] = $prize;
			endif; //Endif
			if( !array_key_exists($campaign,$user_won) ):
				$user_won[$campaign] = $prize;
			endif; //Endif			
			update_user_meta($user, 	'_referral_won', $user_won); //Update Won Capaigns
			update_post_meta($campaign, '_referral_winners', $winners); //Update to Campaign for Winner		
			update_post_meta($campaign, 'wpcf-campaign_referral_enable',0); //Disable Referral for Campaign			
			wp_safe_redirect( add_query_arg( array( 'winner' => false, 'won' => $user, 'prize' => $prize ) ) );
			exit;
		endif; //Endif
	}
	/**
	 * Admin Script
	 *
	 * Handles to admin scripts
	 **/
	public function register_admin_scripts( $hook ){
		
		if( $hook !== 'product_page_referred-upvotes' ) return;
		//Load Admin Script
		wp_enqueue_script('vlog-ref-admin-script', VLOGREF_PLUGIN_URL . '/assets/js/script-admin.js', array('jquery'), NULL, true );
	}
	/**
     * Add Products Column
	 *
     * @since Vlog Referral 1.0
     **/
    public function referral_product_admin_columns( $columns ){	
		return array_merge( $columns, 
					array( 'referred_upvotes' => __('Referred Upvotes', 'vlog-referral') ) );
	}
	/**
     * Add Products Column Data
	 *
     * @since Vlog Referral 1.0
     **/
    public function referral_product_admin_columns_data( $column, $post_id ){
		global $wpdb;
		$table_name = VLOG_REFERRAL_TABLE;
		switch ( $column ) {
			case 'referred_upvotes' :				
				$sql = "SELECT COUNT(upvoted) AS upvotes, referred_by FROM $table_name 
						WHERE 1=1 AND campaign='%d' AND upvoted=1 
						GROUP BY campaign, referred_by ORDER BY upvotes DESC LIMIT 0,1";
				$camp_topper = $wpdb->get_row( $wpdb->prepare( $sql, $post_id ), ARRAY_A );
				if( !empty( $camp_topper ) ) : //Check Topper					
					$user = get_userdata( $camp_topper['referred_by'] );
					printf('<a href="%1$s" target="_blank">%2$s (%3$d)</a>', get_edit_user_link($user->ID), $user->user_login, $camp_topper['upvotes'] );
				else : //Else
					echo '&mdash;';	
				endif; //Endif
				break;
		}
	}
}
//Run Class
$vlogref_admin = new Vlogref_Admin();
endif;