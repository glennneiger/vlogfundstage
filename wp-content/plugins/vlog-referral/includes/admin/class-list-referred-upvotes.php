<?php
/**
 * Referred Upvotes Listing
 *
 * Handles to referred upvote listing
 *
 * @since Vlog Referral 1.0
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if( !class_exists('Vlog_Referred_Upvotes') ) :
class Vlog_Referred_Upvotes extends WP_List_Table {
	
	public function __construct(){
		parent::__construct(
			array( 'singular' => __( 'Referred Upvote', 'vlog-referral' ), //singular name of the listed records
			'plural' => __( 'Referred Upvotes', 'vlog-referral' ), //plural name of the listed records
			'ajax' => false //should this table support ajax?	
		) );
	}
	/**
	 * List Table Columns
	 **/
	public function get_columns(){
		$columns = array(
			'position'	=> __('Position','vlog-referral'),
			'campaign'	=> __('Campaign','vlog-referral'),
			'referred_upvotes'	=> __('Referred Upvotes','vlog-referral'),
			'total_upvotes'	=> __('Total Upvotes','vlog-referral'),			
			'total_signup'	=> __('Total Referred Signup','vlog-referral'),
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
		$table_name = VLOG_REFERRAL_TABLE;		
		$rankings = $wpdb->get_results( "SELECT COUNT(upvoted) AS upvotes, campaign
										FROM $table_name WHERE 1=1 AND upvoted=1
										GROUP BY campaign 
										ORDER BY upvotes DESC;", ARRAY_A );	
		$counter = 1;		
		foreach( $rankings as $rank ) :
			$total_signup 	= $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 AND campaign='".$rank['campaign']."';");
			$total_upvotes	= vlogref_campaign_upvotes($rank['campaign']);
			$campaign_goal 	= vlogref_campaign_upvotes_goal($rank['campaign']);
			$data[] = array( 'position' 	=> $counter,
							'campaign' 		=> sprintf('<a href="%1$s">%2$s</a>', add_query_arg( array( 'page' => 'referred-upvotes', 'campaign' => $rank['campaign'] ), admin_url('edit.php?post_type=product') ), get_the_title( $rank['campaign'] ) ),
							'total_upvotes' => $total_upvotes.'/'.$campaign_goal,
							'total_signup' 	=> $total_signup,
							'referred_upvotes' => $rank['upvotes']
						);
			$counter++;
		endforeach; //Endforeach
		return $data;
	  
	}
	
	/**
	 * List Table Columns Default
	 **/
	public function column_default( $item, $column_name ) {
 
		switch( $column_name ) { 
			case 'position':
				return '<strong>#'.$item[$column_name].'</strong>'; 
			case 'campaign':
			case 'total_upvotes': 
			case 'referred_upvotes':
			case 'total_signup': 
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
		echo '<h1 class="wp-heading-inline">'.__('Referred Upvotes','vlog-referral').'</h1>';
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
$vlog_referred_upvotes = new Vlog_Referred_Upvotes();
$vlog_referred_upvotes->prepare_items();
$vlog_referred_upvotes->display();
endif;