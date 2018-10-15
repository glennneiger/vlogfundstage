/* Admin jQuery
/*-----------------------------------------------------------*/
;(function($, window, document, undefined) {
	
	
	//Update Channels Button
	$('a.update-channels-btn').on('click', function(){
		var total_count = 0;
		var counter = 1;
		///setInterval(function() {
			$.ajax({
				url: ajaxurl,
				data: { action: 'ytc_update_channels', secure: YTC_Admin_Obj.secure, counter: counter },
				dataType: 'json',
				async:false,
				method: 'POST',
				beforeSend: function(){
					$('.result-count, .update-channel-progress-wrap').show();
					$('.result-count').find('.total').html(0);
					$('.result-count').find('.updated').html(0);
				},
				success: function(result){					
					if( result.success == 1 ){						
						if( result.total_count ){
							$('.result-count').find('.total').html( result.total_count );
							total_count = result.total_count;
						}
						if( result.updated ){
							var last_updated = $('.result-count').find('.update').html();
							$('.result-count').find('.updated').html( result.updated + last_updated );
						}
						counter++;
					}
				}
			});
		//}, 3000); //3 seconds
		return false;
	});
	
/*-----------------------------------------------------------*/
})(jQuery, window, document);