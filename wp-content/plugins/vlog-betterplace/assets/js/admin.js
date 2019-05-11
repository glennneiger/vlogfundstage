/* Admin jQuery
/*-----------------------------------------------------------*/
;(function($, window, document, undefined) {
	
	//Update Youtube Channels
	var channelUpdateInt;
	//Update Channels Button
	$('a.import-update-betterplace-orgs').on('click', function(){
		$('.betterplace-result-count, .update-betterplace-orgs-progress-wrap, .betterplace-updated, .betterplace-imported').show();
		var page = 0;
		var updated = 0;
		var imported = 0;
		channelUpdateInt = setInterval(function(){
			var total_count = parseInt( $('.betterplace-result-count').find('.total').html() );		
			$.ajax({
				url: ajaxurl,
				data: { action: 'vlog_betterplace_orgs_import_update', secure: Vlogbet_Admin_Obj.secure, page: page, updated: updated, imported: imported },
				dataType: 'json',
				async:false,
				method: 'POST',				
				statusCode: {
					500: function() {
						var errorok = confirm('There is something wrong, AJAX response failure, please try after sometime!');
						if( errorok == true ){						
							location.reload();
						}
					}
				},
				success: function(result){
					//alert(result);
					//return false;
					if( result.success == 1 ){
						var total_updated = parseInt( result.updated ) + parseInt( result.imported );
						$('.betterplace-result-count').find('.updated').text( total_updated );
						$('.betterplace-result-count').find('.total').text( result.total_entries );
						if( result.updated ){							
							var progress = ( ( total_updated * 100 ) / result.total_entries );
							$('.update-betterplace-orgs-progress').css('width', progress + '%');							
							$('.betterplace-updated').find('.count').text( result.updated );
							page = result.page;
							updated = result.updated;
						}
						if( result.updated_msg ){
							$('.update-betterplace-orgs-results').prepend(result.updated_msg);
						}
						if( result.imported ){
							$('.betterplace-imported').find('.count').text( result.imported );
							imported = result.imported;
						}
						if( result.all_updated ){
							clearInterval(channelUpdateInt);
							alert('All records updated successfully.');
							setTimeout(function(){								
								location.reload(true);
							}, 300);
						}
					} else {
						var errorok = confirm('There is something wrong, AJAX response failure, please try after sometime!');
						if( errorok == true ){						
							location.reload(true);
						}
					}
				}
			});
		}, 3000);		
		return false;
	});
	
/*-----------------------------------------------------------*/
})(jQuery, window, document);