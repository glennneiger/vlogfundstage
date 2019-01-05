/* Custom General jQuery
/*--------------------------------------------------------------------------------------------------------------------------------------*/
;(function($, window, document, undefined) {
	
	//Document Ready
	$(document).ready(function() {
		//Get Query Variable
		var updateQueryStringParameter = function(uri, key, value) {
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";
			if( uri.match(re) ){
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			} else {
				return uri + separator + key + "=" + value;
			}
		};
		
		//Fill Winner Process Data
		$('a.make-winner').on('click', function(){
			var $user_id = $(this).data('user');
			var $user_name = $(this).data('username');
			var $popup_wrap = $('#winner-process .vf-ref-popup-wrapper');
			var $prize = $popup_wrap.find('input[name^="winning_prize"]').val();
			var $declareurl = $popup_wrap.find('.declare-btn').attr('href');
			$declareurl = updateQueryStringParameter($declareurl,'prize', $prize);
			$declareurl = updateQueryStringParameter($declareurl,'winner', $user_id);			
			$('#winner-process').css({'visibility': 'visible', 'opacity': 1});
			$popup_wrap.find('input#winning_user').val($user_id);
			$popup_wrap.find('h2.popup-title').find('span.username').html($user_name);
			$popup_wrap.find('.declare-btn').attr('href', $declareurl);
			return false;
		});
		//Close Popup
		$('#winner-process .close').on('click', function(){
			$('#winner-process').css({'visibility': 'hidden', 'opacity': 0});
			return false;
		});
		//Update URL for Prize
		$('#winner-process input[name^="winning_prize"]').on('change', function(){
			var $prize = $(this).val();
			var $popup_wrap = $('#winner-process .vf-ref-popup-wrapper');
			var $user_id = $popup_wrap.find('input#winning_user').val();
			var $declareurl = $popup_wrap.find('.declare-btn').attr('href');
			$declareurl = updateQueryStringParameter($declareurl,'prize', $prize);
			$declareurl = updateQueryStringParameter($declareurl,'winner', $user_id);
			$popup_wrap.find('.declare-btn').attr('href', $declareurl);
			return false;
		});
		
	});	
/*--------------------------------------------------------------------------------------------------------------------------------------*/
})(jQuery, window, document);