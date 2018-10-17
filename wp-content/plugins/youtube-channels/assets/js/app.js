/* Custom General jQuery
/*-----------------------------------------------------------*/
;(function($, window, document, undefined) {

	var search = false;
	var count = 0;

	$(document).ready(function() {

		//Show Tweets Click
		$('.showtweets').on('click', function(){
			$('#detailtitle').html($(this).attr('data-title'));
			$('#cvideos').hide();
			$('#tweets').show();
		});

		//Show Videos Click
		$('.showvideos').on('click', function(){
			$('#detailtitle').html($(this).attr('data-title'));
			$('#tweets').hide();
			$('#cvideos').show();
		});

		//Youtubes Click
		$(document).on('click', '.youtubes', function(){
			var iframe = document.createElement( "iframe" );
			iframe.setAttribute( "frameborder", "0" );
			iframe.setAttribute( "allowfullscreen", "" );
			iframe.setAttribute( "src", "https://www.youtube.com/embed/"+ this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );
			this.innerHTML = "";
			this.appendChild( iframe );
		});

		//LazyLoad Images
		var blazy = new Blazy();

		//Get URL Parameter
		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = decodeURIComponent(window.location.search.substring(1)),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;
			for( i = 0; i < sURLVariables.length; i++ ){
				sParameterName = sURLVariables[i].split('=');
				if ( sParameterName[0] === sParam ){
					return sParameterName[1] === undefined ? true : sParameterName[1];
				}
			}
		};

		//Search Auto Complete
		$("#ytc-search-input").autocomplete({
			source: YTC_Obj.ajaxurl + '?action=ytc_search_autocomplete',
			minLength: 2,
			select: function(event, ui) {
				if( search == false ){
					$('#ytc-channles-list').empty();
					$("#homelink").text("Clear Search");
					search = true;
				}
				$.ajax({
					url: YTC_Obj.ajaxurl, //"ajax/getdata",
					data : { action: 'ytc_get_channels', channelid: ui.item.channelid, offset:0 },
					type: 'POST',
					beforeSend: function(){
						$(".tag").append('<a href="#">'+ui.item.label+' <span data-channelid="'+ui.item.channelid+'" class="removebtn" style="margin-left:15px;font-size:20px">x</span></a>');
						$('#ytc-channles-list').hide();
						$('#ytc-searchloader').show();
						$('#ytc-loadmore').hide();
					},
					success : function(data){
						if( data ){
							$('#ytc-search-input').val('');
							$('#ytc-searchloader').hide();
							$('#ytc-channles-list').html(data);
							$('#ytc-channles-list').show();
							count++;
							new Blazy(); //LazyLoad Images
						}else{
							$('#ytc-channles-list').html('No records found');
						}
					},
				});
			}
		});

		//Remove Button Click
		$(document).on('click', '.removebtn', function(e){
			e.preventDefault();
			var channelid = $(this).attr('data-channelid');
			$("#"+channelid).remove();
			$(this).parent().remove();
			count--;
			if( count == 0 ){
			   window.location.reload();
			}
		});

		//Load More		
        $(document).on('click', '#ytc-loadmore', function(e) {
            e.preventDefault();
			var $search = $('#ytc-search-input').val();
			var $orderby = $('#ytc-orderBy').val();
			var $sortby = $('#ytc-sortBy').val();
			var $paged = $('#ytc-page').val();
            var elem = $(this);
            elem.prop('disabled', true);
            elem.html('<i class="fa fa-spinner fa-spin"></i> Loading');			
			$.ajax({
				url: YTC_Obj.ajaxurl,
				data : { action: 'ytc_loadmore_channels', paged: $paged, search: $search, order: $orderby, sort: $sortby },
				type: 'POST',
				success : function(result){
					if( result.html ){
						elem.prop('disabled', false);
						elem.html('Load More');
						$(result.html).insertBefore( $('#ytc-channles-list').find('.sfc-campaign-archive-post:first') );
						new Blazy(); //LazyLoad Images
					}else{
						elem.html('No more records');
					}
					if( result.more_page ){ //More
						$('#ytc-loadmore').show();
					} else {
						$('#ytc-loadmore').hide();
					}
					if( result.paged ){
						$('#ytc-page').val(result.paged);
					}
				},
			});
        });
		
		//On Submit of Search Form
		$('#ytc-search-form').on('submit', function(){
			var $search = $('#ytc-search-input').val();
			var $orderby = $('#ytc-orderBy').val();
			var $sortby = $('#ytc-sortBy').val();			
			if( typeof $search !== 'undefined' && $search !== '' ){
				$.ajax({
					url: YTC_Obj.ajaxurl,
					data : { action: 'ytc_search_channels', search: $search, order: $orderby, sort: $sortby },
					type: 'POST',
					beforeSend: function(){
						$('#ytc-channles-list, #ytc-loadmore, #ytc-creators-wrap').hide();
						$('#ytc-searchloader').show();
					},
					complete: function(){
						$('#ytc-searchloader').hide();
						$('#ytc-channles-list, #ytc-loadmore, #ytc-creators-wrap').show();						
					},
					success : function(result){
						if( result.html ){							
							$('#ytc-channles-list').html(result.html);
							$('#ytc-page').val(1);
							new Blazy(); //LazyLoad Images
						} else {
							elem.html('No more records');
						}
						if( result.more_page ){ //More
							$('#ytc-loadmore').show();
						} else {
							$('#ytc-loadmore').hide();
						}
						if( result.found_posts ){ //More
							$('#ytc-creators-wrap').show().find('.count').html(result.found_posts);
						}
					},
				});
			}
			return false;
		});
	});
/*-----------------------------------------------------------*/
})(jQuery, window, document);
