<?php
/**
 * Referred Donations Listing
 *
 * Handles to referred donations
 *
 * @since Vlog Referral 1.0
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if( !class_exists('Vlog_Referred_Donations') ) :

class Vlog_Referred_Donations extends WP_List_Table {
	
	public function __construct(){
		parent::__construct(
			array( 'singular' => __( 'Referred Donation', 'vlog-referral' ), //singular name of the listed records
			'plural' => __( 'Referred Donations', 'vlog-referral' ), //plural name of the listed records
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
			'referred_donations'=> __('Total Referred Donations','vlog-referral'),
			'total_donations'	=> __('Total Donations','vlog-referral'),			
			'referred_orders'	=> __('Total Referred Orders','vlog-referral'),
			'total_orders'		=> __('Total Orders','vlog-referral'),
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
		$donations = $wpdb->get_results( "SELECT SUM(amount) AS donation, COUNT(*) AS referred_orders, campaign
										FROM $table_name WHERE 1=1 AND donated=1
										GROUP BY campaign 
										ORDER BY donation DESC;", ARRAY_A );		
		if( !empty( $donations ) ) : //Check Data Empty
			$counter = 1;
			foreach( $donations as $donation ) :
				$total_donation = vlogref_donations_get_campaign_total( $donation['campaign'] );				
				$data[] = array( 'position' 	=> $counter,
								'campaign' 		=> sprintf('<a href="%1$s">%2$s</a>', add_query_arg( array( 'page' => 'referred-donations', 'campaign' => $donation['campaign'] ), admin_url('edit.php?post_type=product') ), get_the_title( $donation['campaign'] ) ),
								'referred_donations'=> vlogref_price( $donation['donation'] ),
								'total_donations' 	=> vlogref_price( $total_donation['total_amount'] ),
								'referred_orders' 	=> $donation['referred_orders'],
								'total_orders'		=> $total_donation['total_orders']
							);
				$counter++;
			endforeach; //Endforeach
		endif; //Endif
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
			case 'referred_donations': 
			case 'total_donations':
			case 'referred_orders':
			case 'total_orders': 
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
		echo '<h1 class="wp-heading-inline">'.__('Referred Donations','vlog-referral').'</h1>';
		$columns 	= $this->get_columns();
		$data 		= $this->table_data();
		$totalitems = count($data);
		$this->_column_headers = array($columns);
		$perpage 	= 20;			
		$totalpages = ceil($totalitems/$perpage); 
		$currentPage= $this->get_pagenum();
		$data 		= array_slice( $data,( ( $currentPage - 1 ) * $perpage ),$perpage);		
		$this->set_pagination_args( array(
			 'total_items' => $totalitems,
			 'total_pages' => $totalpages,
			 'per_page' => $perpage,
		) );
		$this->items = $data;		
    }
}
$vlog_referred_donations = new Vlog_Referred_Donations();
$vlog_referred_donations->prepare_items();
$vlog_referred_donations->display();
endif;