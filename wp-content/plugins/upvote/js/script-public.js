/* Custom jQuery Functions
/*--------------------------------------------------------------------------------------------------------------------------------------*/
;(function($, window, document, undefined) {

	//Document Ready
	$(document).ready(function(e) {

		//Upvote Button Click
		$(document).on('click', '.upvote-btn.vote-me', function(){
			var $this = $(this);
			var $postid = $this.data('id');
			var $upvote = $('.upvote-count[data-id^="'+$postid+'"]');
			var $othervotebtn = $('.upvote-btn.vote-me[data-id^="'+$postid+'"]').not('.icon').not($this);
			var $othericonvotebtn = $('.upvote-btn.icon.vote-me[data-id^="'+$postid+'"]').not($this);
			var $upvotecountcont = $('span.upvote-count-sc[data-id^="'+$postid+'"]')
			if( $this.parents('.upvote-progress-button').hasClass('success-upvote') ){
				return false;
			}
			if( $this.hasClass('icon') ) { //Icon Button Click
				var $fill = $this.find('i').data('fill');
				$.ajax({
					url: Upvote.ajaxurl,
					method:'POST',
					data:{ action: 'upvote_update_vote', postid: $postid },
					beforeSend: function(){
						$this.attr('disabled',true);
					},
					complete: function(){
					},
					success: function(response){
						//alert(response);
						var result = $.parseJSON(response);
						if( result.success == '1' ){
							$this.parents('.upvote-progress-button').addClass('success-upvote');
							$this.find('span').html(result.count);
							$upvote.find('span').html(result.count); //For all Upvote on page for same post
							$upvotecountcont.html(result.count);
							//Other Vote Button
							$othericonvotebtn.find('span').html('↑ ' + result.count).attr('disabled','disabled');
							$othericonvotebtn.parents('.upvote-progress-button').addClass('success-upvote');
							$othervotebtn.text(result.message).attr('disabled','disabled');
							$othervotebtn.parents('.upvote-progress-button').addClass('success-upvote');
							$this.show();
							//Upvote Custom Event
							$this.trigger('upvote', result);
                            toastr.success('', 'Thank you for voting!');
							window.dataLayer = window.dataLayer || [];
							dataLayer.push({'event': 'upvote'});
							if( result.guest_limit_reached == '1' && Upvote.product_page != '1' ) {
								$('.upvote-btn').wrap('<a href="#register"></a>').removeClass('vote-me').removeAttr('data-id');
								//location.reload();
							}
						} else if( result.voted == '1' ){
							$this.find('i').addClass($fill);
							$this.remove();
						} //Endif
					} //Endif
				});
			} else { //Normal Button Click
				//var $upvote = $this.parents('.upvote-container, .upvote-container-big').find('.upvote-count');
				$.ajax({
					url: Upvote.ajaxurl,
					method:'POST',
					data:{ action: 'upvote_update_vote', postid: $postid },
					beforeSend: function(){
						$this.attr('disabled',true);
					},
					complete: function(){
					},
					success: function(response){
						//alert(response);
						var result = $.parseJSON(response);
						if( result.success == '1' ){
							$this.parents('.upvote-progress-button').addClass('success-upvote');
							$this.text(result.message);
							$upvote.html('+ '+result.count);
							$upvote.find('span').html('+ ' + result.count); //For all Upvote on page for same post
							$upvotecountcont.html(result.count);
							//Other Vote Button
							$othericonvotebtn.find('span').html('↑ ' + result.count).attr('disabled','disabled');
							$othericonvotebtn.parents('.upvote-progress-button').addClass('success-upvote');
							$othervotebtn.text(result.message).attr('disabled','disabled');
							$othervotebtn.parents('.upvote-progress-button').addClass('success-upvote');
							toastr.success('', 'Thank you for voting');
							$this.show();
							setTimeout(function() {
								jQuery('#share.sf-popup').addClass('share-popup-visible');
							}, 1500);
							window.dataLayer = window.dataLayer || [];
							dataLayer.push({'event': 'upvote'});
							//Upvote Custom Event
							$this.trigger('upvote', result);
							if( result.guest_limit_reached == '1' && Upvote.product_page != '1' ) {
								$('.upvote-btn').wrap('<a href="#register"></a>').removeClass('vote-me').removeAttr('data-id');
								location.reload();
							}
						} else if( result.voted == '1' ){
							$('<button disabled="disabled">' + result.message + '</button>').insertAfter( $this );
							$this.remove();
						} //Endif
					} //Endif
				});
			}
			return false;
		});
		//Click on Upvote Count to Vote
		$(document).on('click', '.upvote-container-big .upvote-count', function(){
			$(this).parents('.upvote-container-big').find('button.upvote-btn.vote-me').trigger('click');
			return false;
		});

		//Click on Register Button
		$(document).on('click', 'a[href^="#register"]', function(e){
			e.preventDefault();
			if( Upvote.logged_in == 0 ) {
				$('a[href^="#register"]').trigger('click');
			}
		});
    });

/*--------------------------------------------------------------------------------------------------------------------------------------*/
})(jQuery, window, document);
