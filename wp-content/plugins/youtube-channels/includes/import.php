<?php
/**
 * Import Channels
 *
 * Handles import channels page
 *
 * @since YouTube Channels 1.0 
 **/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	$import_msg = '';

	if( isset( $_POST['ytc-import-submit'] ) && !empty( $_POST['ytc-import-submit'] )
		&& isset( $_FILES['ytc-import-file'] ) && !empty( $_FILES['ytc-import-file'] ) ) :

		global $wpdb;

		$table_name = $wpdb->prefix . 'yt_channels';
		
		$ext = explode('.', $_FILES['ytc-import-file']['name']); // For getting Extension of selected file
		$extension = end( $ext );
		$allowed_extension = array('xls', 'xlsx', 'csv'); //allowed extension
		if( in_array( $extension, $allowed_extension ) ) { //check selected file extension is present in allowed extension array
			$file = $_FILES['ytc-import-file']['tmp_name']; // getting temporary source of excel file
			require_once( YTC_PLUGIN_PATH . 'includes/PHPExcel/PHPExcel/IOFactory.php' ); // Add PHPExcel Library in this code
			$objPHPExcel = PHPExcel_IOFactory::load($file); // create object of PHPExcel library by using load() method and in load method define path of selected file
			$import_msg .= '<div class="notice notice-success">';
			$import_msg .= '<strong>'.__('Data Inserted','youtube-channels').'</strong>';
			$import_msg .= '</div><table>';
			$i = 1;
			foreach( $objPHPExcel->getWorksheetIterator() as $worksheet ) {
				$highestRow = $worksheet->getHighestRow();
				for( $row = 2; $row <= $highestRow; $row++ ) {
					$import_msg .= '<tr>';
						$url	= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(0, $row)->getValue() );
						$fb 	= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(1, $row)->getValue() );
						$insta	= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(2, $row)->getValue() );
						$twitter= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(3, $row)->getValue() );
						$gp		= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(4, $row)->getValue() );
						$wb 	= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(5, $row)->getValue() );
						$sp		= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(6, $row)->getValue() );
						$vk 	= $wpdb->_real_escape( $worksheet->getCellByColumnAndRow(7, $row)->getValue() );
						if( !empty( $url ) ){
							$pos = strrpos( $url, '/' );
							$channelid = $pos === false ? $url : substr($url, $pos + 1);
							$import_msg .= '<td>'.$i.'</td>';
							$import_msg .= '<td>'.$channelid.'</td>';
							$import_msg .= '<td>'.$insta.'</td>';
							$import_msg .= '<td>'.$fb.'</td>';
							$import_msg .= '<td>'.$twitter.'</td>';
							$import_msg .= '<td>'.$gp.'</td>';
							$import_msg .= '<td>'.$wb.'</td>';
							$import_msg .= '<td>'.$sp.'</td>';
							$import_msg .= '<td>'.$vk.'</td>';
							$i++;								
							$update_data = array( 'facebook' => $fb, 'twitter' => $twitter, 'website' => $wb, 'snapchat' => $sp, 'gplus' => $gp, 'vk' => $vk, 'instagram' => $insta );
							$wpdb->update( $table_name, $update_data, array( 'channelid' => $channelid ) );								
						}					
					$import_msg .= '</tr>';
				}
			} 
			$import_msg .= '</table>';
		} else {
			$import_msg = '<div class="notice notice-error"><strong>ERRROR:</strong>Invalid file type, please choose correct file.</div>'; //if non excel file then
		}
	endif; //Endif

?>
	<div class="wrap">
        <h1><?php _e( 'Import Channels', 'youtube-channels' ); ?></h1>
		<?php if( !empty( $import_msg ) ) : //Import Message
				echo $import_msg;
			endif; //Endif ?>
		<form action="admin.php?page=ytc-channels" method="post" enctype="multipart/form-data">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php _e('File');?></th>
						<td><input type="file" name="ytc-import-file" class="form-control"></td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( __('Import','youtube-channels'), 'primary', 'ytc-import-submit' );?>
		</form>
    </div><!--/.wrap-->