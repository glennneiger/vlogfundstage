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
							//Other Vote Button
							$othericonvotebtn.find('span').html('+ ' + result.count).attr('disabled','disabled');
							$othericonvotebtn.parents('.upvote-progress-button').addClass('success-upvote');
							$othervotebtn.text(result.message).attr('disabled','disabled');
							$othervotebtn.parents('.upvote-progress-button').addClass('success-upvote');
							$this.show();
							//Upvote Custom Event
							$this.trigger('upvote', result);
                            toastr.success('', 'Thank you for voting!');
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
							//Other Vote Button
							$othericonvotebtn.find('span').html('+ ' + result.count).attr('disabled','disabled');
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
						} else if( result.voted == '1' ){
							$('<span class="already-voted">' + result.message + '</span>').insertAfter( $this );
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
    });

/*--------------------------------------------------------------------------------------------------------------------------------------*/
})(jQuery, window, document);
