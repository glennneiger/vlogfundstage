<?php
/**
 * Paginator Class
 *
 * Handles to generate paginator class
 *
 * @since Vlog Referral 1.0
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( !class_exists('Vlogref_Paginator') ) :
class Vlogref_Paginator {
 
	private $_limit;
	private $_page;
	private $_query;
	private $_total;
	
	public function __construct( $query ) {
		global $wpdb;
    	$this->_query = $query;
		$this->_limit = 20;
		$this->_page = ( isset( $_GET['pg'] ) && !empty( $_GET['pg'] ) ) ? $_GET['pg'] : 1;		
		$wpdb->query( $this->_query );		
		$this->_total = $wpdb->num_rows;
	}
	
	public function createLinks() {

		if( $this->_limit == 'all' ){
			return '';
		}
	 	$links 	= 5;
		$last	= ceil( $this->_total / $this->_limit );	 
		$start  = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
		$end    = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;
		$html = '';		
		if( $start > 1 && $start != $last ) :
		
			$html   .= '<ul class="wpv-pagination-nav-links-container js-wpv-pagination-nav-links-container pagination">';		
				if( $this->_page != 1 ) :
					$html   .= '<li class="wpv-pagination-nav-links-item js-wpv-pagination-nav-links-item page-item wpv-page-link-232-TCPID226-1 js-wpv-page-link-232-TCPID226-1 ' . $class . '"><a href="'.add_query_arg('pg',( $this->_page - 1 )).'"><i class="fa fa-angle-left"></i></a></li>';
				endif; //Endif
			 
				if( $start > 1 ) :
					$html   .= '<li><a href="'.add_query_arg('pg',1).'">1</a></li>';
					$html   .= '<li class="disabled"><span>...</span></li>';
				endif;
			 
				for( $i = $start ; $i <= $end; $i++ ) :
					if( $this->_page == $i ) :
						$html   .= '<li class="wpv-pagination-nav-links-item js-wpv-pagination-nav-links-item page-item wpv-page-link-232-TCPID226-1 js-wpv-page-link-232-TCPID226-1 wpv_page_current wpv-pagination-nav-links-item-current active"><span class="wpv-filter-pagination-link">' . $i . '</span></li>';
					else :
						$html   .= '<li class="wpv-pagination-nav-links-item js-wpv-pagination-nav-links-item page-item wpv-page-link-232-TCPID226-1 js-wpv-page-link-232-TCPID226-1 ' . $class . '"><a href="'.add_query_arg('pg',$i).'">' . $i . '</a></li>';
					endif; //Endif
				endfor;
			 
				if ( $end < $last ) :
					$html   .= '<li class="disabled"><span>...</span></li>';
					$html   .= '<li><a href="'.add_query_arg('pg',$last).'">' . $last . '</a></li>';
				endif; //Endif
				
				if( $this->_page != $last ) :
					$html  .= '<li class="wpv-pagination-nav-links-item js-wpv-pagination-nav-links-item page-item wpv-page-link-232-TCPID226-1 js-wpv-page-link-232-TCPID226-1 ' . $class . '"><a href="'.add_query_arg('pg',( $this->_page + 1 )).'"><i class="fa fa-angle-right"></i></a></li>';
				endif; //Endif		 
			$html  .= '</ul>';

		endif; //Endif
	 
		return $html;
	}
 
}
endif; //Endif