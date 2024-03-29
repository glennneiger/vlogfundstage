/* Admin jQuery
/*-----------------------------------------------------------*/
;(function($, window, document, undefined) {
	
	//Update Youtube Channels
	var channelUpdateInt;
	//Update Channels Button
	$('a.update-channels-btn').on('click', function(){
		$('.result-count, .update-channel-progress-wrap').show();
		var paged = 1;
		var updated = 0;
		channelUpdateInt = setInterval(function(){
			var total_count = parseInt( $('.result-count').find('.total').html() );		
			$.ajax({
				url: ajaxurl,
				data: { action: 'ytc_update_channels', secure: YTC_Admin_Obj.secure, paged: paged, updated: updated, total:total_count },
				dataType: 'json',
				async:false,
				method: 'POST',
				success: function(result){
					if( result.success == 1 ){
						var last_updated = parseInt( $('.result-count').find('.updated').text() );
						if( result.updated ){
							var total_updated = parseInt( result.updated );
							$('.result-count').find('.updated').text( total_updated );
							var progress = ( ( total_updated * 100 ) / total_count );
							$('.update-channel-progress').css('width', progress + '%');
							paged = result.paged;
							updated = result.updated;
							if( total_updated >= total_count ){
								clearInterval(channelUpdateInt);
								alert('All records updated successfully.');
							}
						}						
					}
				}
			});
		}, 3000);		
		return false;
	});
	
/*-----------------------------------------------------------*/
})(jQuery, window, document);