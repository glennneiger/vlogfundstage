/* Custom General jQuery
/*-----------------------------------------------------------*/
;(function($, window, document, undefined) {
	
	var search = false;
	var count = 0;
	
	$(document).ready(function() {

		//Show Tweets Click
		$(".showtweets").on('click', function(){
			$("#detailtitle").html($(this).attr('data-title'));
			$("#cvideos").hide();
			$("#tweets").show();
		});
	
		//Show Videos Click
		$(".showvideos").on('click', function(){
			$("#detailtitle").html($(this).attr('data-title'));
			$("#tweets").hide();
			$("#cvideos").show();
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
		
		//Init Function
		var init = function(){
			var imgDefer = document.getElementsByTagName('img');
			for( var i = 0; i < imgDefer.length; i++ ){
				if (imgDefer[i].getAttribute('data-src')) {
					imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
				}
			}
		
			$('.details').tooltipster({
				delay: 10,
				contentAsHTML: true,
				maxWidth: 500,
				interactive: true,
				multiple: false,
				//delay: [0, 200],
				trigger: 'custom',
				triggerOpen: {
					mouseenter: true,
					touchstart: true
				},
				triggerClose: {
					click: true,
					mouseleave: true,
					tap: true
				}
			});
		};
		window.onload = init();
		
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
				$('#ytc-channles-list').hide();
				$('#ytc-searchloader').show();
				$('#ytc-loadmore').hide();				
				$.ajax({
					url: YTC_Obj.ajaxurl, //"ajax/getdata",
					data : { action: 'ytc_get_channels', channelid: ui.item.channelid, offset:0 },
					type: 'POST',
					beforeSend: function(){
						$(".tag").append('<a href="#">'+ui.item.label+' <span data-channelid="'+ui.item.channelid+'" class="removebtn" style="margin-left:15px;font-size:20px">x</span></a>');
					},
					success : function(data){
					 if( data ){
						$("#ytc-search-input").val('');						
						$('#ytc-searchloader').hide();
						$('#ytc-channles-list').append(data);
						$('#ytc-channles-list').show();
						count++;
						init();
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
		
		//Show Image Information
		$(document).on('click','.showinfoimg',function(e){
			e.preventDefault();
			$(this).parent().find('.showinfo').trigger('click');
		});
		
		//Show Information
		$(document).on('click','.showinfo',function(e){
			e.preventDefault();
			var title = $(this).attr('data-title');
			var channelid = $(this).attr('data-channelid');
			var subs = $("#"+channelid+'-subs').text();
			var img = $("#"+channelid+'-img').attr('src');
			var views = $("#"+channelid+'-views').text();
			var instagram = $(this).attr('data-instagram');
			var twitter = $(this).attr('data-twitter');
			$(".showtweets").attr('data-twitter', channelid);
			$("#ctitle").html(title);
			$("#csubs").html(subs);
			$("#cviews").html(views);
			$("#cimg").attr('src', img);
			$("#videoloader").show();
			$("#cvideos").empty();
			$('#infomodal').modal('show');
			$("#tweets").hide();
			$("#cvideos").show();
			$("#detailtitle").html('Latest Videos');
			if( instagram!='' ){
				$(".instagram").attr('href', instagram);
				$(".instagram").show();
			}else{
				$(".instagram").hide();
			}
		
			if( twitter!='' ){
				$("#tweets").empty();
				$("#tweets").html('<a class="twitter-timeline" href="https://twitter.com/'+twitter+'?ref_src=twsrc%5Etfw">Tweets by '+twitter+'</a>');
				$("#tweets").append($("<script />", { src: 'https://platform.twitter.com/widgets.js' }));
				$('.showtweets').show();
			}else{
				$('.showtweets').hide();
			}
		
			$.ajax({
				url: YTC_Obj.ajaxurl, //"ajax/ajaxgetvideos",
				type: 'POST',
				data: { action: 'ytc_get_channel', id:channelid },
				success : function(data){
					if( data ){
						$("#videoloader").hide();
						$("#cvideos").html(data);
					}
				},
			});
		});
		
		//On Add Channel Submit		
		$('#addchannelform').on('submit', function (e) {
			var btn = $("#addchannelbtn");
			e.preventDefault();
			$("#msg").html('');
			btn.prop('disabled', true);
			btn.html('<i class="fa fa-spinner fa-spin"></i>');
			$.ajax({
				type: 'POST',
				url: YTC_Obj.ajaxurl, //'ajax/ajaxaddchannel.php',
				data: $('#addchannelform').serialize() + '&action=ytc_add_channel',
				success: function (data) {
					btn.prop('disabled', false);
					btn.html('Submit');
					$("#msg").html(data);
				}
			});
		});
		
		//Show/Hide Filter	
		var show = false;
		$('#showfilters').on('click', function(e){
			  e.preventDefault();
			  if( show == false ){
				  $("#filters").slideDown();
				  $(this).html('Hide Filters')
				  show = true;
			  }else{
				  $("#filters").slideUp();
				  $(this).html('Show Filters')
				  show = false;
			  }
		});
		
		//Load More
		var offset = 0;
        $(document).on('click', '#ytc-loadmore', function(e) {
            e.preventDefault();
            var elem = $(this);
            elem.prop('disabled', true);
            elem.html('<i class="fa fa-spinner fa-spin"></i> Loading');
            offset += 40;
			$.ajax({
				url: YTC_Obj.ajaxurl, //"ajax/getdata",
				data : { action: 'ytc_get_channels', normal: 1, offset: offset, order: getUrlParameter('orderBy'), sort: getUrlParameter('sortBy') },
				type: 'POST',               
				success : function(data){
					if( data ){
						elem.prop('disabled', false);
						elem.html('Load More');
						$('#ytc-channles-list').append(data);
						//imgLazyLoadAndTooltipser(data);
						var imgDefer = $(data).find('img');
						for( var i = 0; i < imgDefer.length; i++ ){
							if( imgDefer[i].getAttribute('data-src') ){
								imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
							}
						}
					}else{
						elem.html('No more records');
					}
				},
			});
        });
		//Load Lazyload + Tooltipser
		var imgLazyLoadAndTooltipser = function( $obj ){
			var imgDefer = $($obj).filter('img');
			for( var i = 0; i < imgDefer.length; i++ ){
				if( imgDefer[i].getAttribute('data-src') ){
					imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
				}
			}
			$($obj).find('.details').tooltipster({
				delay: 10,
				contentAsHTML: true,
				maxWidth: 500,
				interactive: true,
				multiple: false,
				//delay: [0, 200],
				trigger: 'custom',
				triggerOpen: {
					mouseenter: true,
					touchstart: true
				},
				triggerClose: {
					click: true,
					mouseleave: true,
					tap: true
				}
			});
		};
	});
	
/*-----------------------------------------------------------*/
})(jQuery, window, document);