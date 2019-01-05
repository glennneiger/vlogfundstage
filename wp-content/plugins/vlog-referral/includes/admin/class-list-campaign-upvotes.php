<?php
/**
 * Upvotes Listing for Particular Campaign
 *
 * Handles to upvotes listing for particular campaign
 *
 * @since Vlog Referral 1.0
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if( !class_exists('Vlog_Campaign_Upvotes') ) :
class Vlog_Campaign_Upvotes extends WP_List_Table {
	
	public function __construct(){
		parent::__construct(
			array( 'singular' => __( 'Campaign Upvote', 'vlog-referral' ), //singular name of the listed records
			'plural' => __( 'Campaign Upvotes', 'vlog-referral' ), //plural name of the listed records
			'ajax' => false //should this table support ajax?	
		) );
	}
	/**
	 * List Table Columns
	 **/
	public function get_columns(){
		$columns = array(
			'position'	=> __('Position','vlog-referral'),
			'username'	=> __('Username','vlog-referral'),
			'upvotes'	=> __('Referred Upvotes','vlog-referral'),
			'total_signup'	=> __('Total Referred Signup','vlog-referral'),
			'action'	=> __('Action', 'vlog-referral')
		);
  		return $columns;
	}
	/**
	 * No Records
	 **/
	public function no_items() {
  		_e( 'No records found.', 'vlog-referral' );
	}
	
	/**
	 * List Table Data
	 **/
	private function table_data(){
		global $wpdb;
		$data = array();
		$campaign = $_GET['campaign'];
		if( isset( $_GET['won'] ) && !empty( $_GET['won'] ) ) :
			$userdata = get_userdata( $_GET['won'] );
			$prize = vlogref_get_prize_details( $_GET['prize'] );			
			echo '<div class="updated notice"><p>'.sprintf('<a href="%1$s" target="_blank"><strong>%2$s</strong></a> %3$s <strong>%4$s</strong>', get_edit_user_link( $userdata->ID ), $userdata->user_login, __('declared as winner with prize', 'vlog-referral'), $prize['title'] ).'</p></div>';
		endif; //Endif
		$table_name = VLOG_REFERRAL_TABLE;
		$camp_rankings = vlogref_get_campaign_referrals( array('campaign' => $campaign) );
		$counter = 1;		
		foreach( $camp_rankings as $rank ) :
			$user		= get_userdata($rank['referred_by']);
			$winners	= vlogref_get_campaign_winners($rank['campaign']);
			$won_camp  	= vlogref_get_user_won_campaigns( $user->ID );
			$winner_str = '';
			//Add Winner Button for Top #3
			if( $counter <= 3 && !array_key_exists($user->ID, $winners) ) :
				$winner_str .= sprintf('<a href="#winner-process" class="button-primary make-winner" data-user="%1$s" data-username="%2$s">%3$s</a><br>', $user->ID, $user->user_login, __('Make Winner!', 'vlog-referral') );
			endif; //Endif
			//Check Already Winner of This Campaign
			if( array_key_exists($user->ID, $winners) ) :
				$cur_camp_prize = $winners[$user->ID];
				$cur_prize_details = vlogref_get_prize_details($cur_camp_prize);
				$winner_str .= '<strong style="color:green;">'.sprintf('%1$s - %2$s', __('Won','vlog-referral'), $cur_prize_details['title']).'</strong><br>';
			endif; //Endif
			if( !empty( $won_camp ) ) : //Check Other Campaign Won
				foreach( $won_camp as $camp => $prize ) :
					if( $rank['campaign'] == $camp ) continue;
					$prize_details = vlogref_get_prize_details($prize);
					$winner_str .= sprintf('<a href="%1$s">%2$s</a> - %3$s %4$s<br>', get_permalink( $camp ), get_the_title( $camp ), __('won','vlog-referral'), $prize_details['title'] );
				endforeach; //Endforeach
			endif; //Endif
			$data[] = array( 'position' => $counter,
							'username' 	=> sprintf('<a href="%1$s" target="_blank">%2$s</a>', get_edit_user_link( $user->ID ), $user->user_login ), 
							'upvotes' 	=> $rank['upvotes'],
							'total_signup' => vlogref_user_campaign_referred_signup_count($rank['campaign'], $user->ID),
							'action' 	=> !empty( $winner_str ) ? $winner_str : '&mdash;'
					);
			$counter++;
		endforeach; //Endforeach
		return $data;	  
	}	
	/**
	 * List Table Column Default
	 **/
	public function column_default( $item, $column_name ) { 
		switch( $column_name ) { 
			case 'position':
				return '<strong>#'.$item[$column_name].'</strong>';
			case 'username': 
			case 'upvotes': 
			case 'total_signup':
			case 'action':
				return $item[$column_name];
			 default: 
				return print_r( $item, true ) ; 
		} 
	}
	
	/**
	 * List Table Prepare Items
	 **/
	public function prepare_items(){
 
		global $wpdb;
		$campaign = $_GET['campaign'];
		echo '<h1 class="wp-heading-inline">'.sprintf('%1$s <a href="%2$s" target="_blank">%3$s #%4$d</a>', __('Ranking for','vlog-referral'), get_permalink( $campaign ), get_the_title( $campaign ), $campaign ).'</h1>';		
		$columns = $this->get_columns();		
		$data = $this->table_data();
		$totalitems = count($data);
		$this->_column_headers = array($columns);  
		$perpage = 20;		
		$totalpages = ceil($totalitems/$perpage); 
		$currentPage = $this->get_pagenum();
		$data = array_slice( $data,( ( $currentPage - 1 ) * $perpage ),$perpage);		
		$this->set_pagination_args( array(
			 'total_items' => $totalitems,
			 'total_pages' => $totalpages,
			 'per_page' => $perpage,
		) );			 
		$this->items = $data;		
    }
}
$vlog_campaign_upvotes = new Vlog_Campaign_Upvotes();
$vlog_campaign_upvotes->prepare_items();
$vlog_campaign_upvotes->display();
endif;