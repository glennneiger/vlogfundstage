/* Custom General jQuery
/*--------------------------------------------------------------------------------------------------------------------------------------*/
;(function($, window, document, undefined) {
	//Genaral Global variables
	//"use strict";
	var $win = $(window);
	var $doc = $(document);
	var $winW = function(){ return $(window).width(); };
	var $winH = function(){ return $(window).height(); };
	var $screensize = function(element){
			$(element).width($winW()).height($winH());
		};

		var screencheck = function(mediasize){
			if (typeof window.matchMedia !== "undefined"){
				var screensize = window.matchMedia("(max-width:"+ mediasize+"px)");
				if( screensize.matches ) {
					return true;
				}else {
					return false;
				}
			} else { // for IE9 and lower browser
				if( $winW() <=  mediasize ) {
					return true;
				}else {
					return false;
				}
			}
		};

	$doc.ready(function() {
/*--------------------------------------------------------------------------------------------------------------------------------------*/
		// Remove No-js Class
		$("html").removeClass('no-js').addClass('js');


		/* Get Screen size
		---------------------------------------------------------------------*/
		$win.load(function(){
			$win.on('resize', function(){
				$screensize('your selector');
			}).resize();
		});

		$(window).on('resize', function(){
			if( screencheck(1023)) {
				$('.sf-imgbox-right').each( function(){
					$(this).find('.sf-info-section').insertAfter($(this).find('.sf-stars-img-section'));
				});
			}
			else {
				$('.sf-imgbox-right').each( function(){
					$(this).find('.sf-info-section').insertBefore($(this).find('.sf-stars-img-section'));
				});
			}
		}).resize();


		/*Mobile menu click
		---------------------------------------------------------------------*/
		$("#menu-toggle").on("click", function(){
			$(".sf-navbox").toggleClass("sf-visible");
			$("body").toggleClass("sf-nav-visible");
			$(this).toggleClass("sf-menuopen");
			return false;
		});

		/* Slider Js
		-------------------------------------------------------------------------*/
		if($(".sf-main-slider").length) {
			$('.sf-main-slider').slick({
				infinite: true,
				speed: 700,
				fade: true,
				arrows: false,
				cssEase: 'linear',
				autoplay: true,
  				autoplaySpeed: 3000,
				pauseOnHover:false
			});
		}
		if($(".sf-stars-slider").length) {
			$('.sf-stars-slider').slick({
				dots: true,
				arrows: false,
				autoplay: true,
  				autoplaySpeed: 2000,
				pauseOnHover:false
			});
		}
		if($(".sf-news-slider").length) {
			$('.sf-news-slider').slick({
				dots: true,
				arrows: false,
				infinite: true,
  				slidesToShow: 3,
  				slidesToScroll: 1,
				autoplay: true,
  				autoplaySpeed: 4000,
				pauseOnHover:false,
				responsive: [
					{
					  breakpoint: 1023,
					  settings: {
						slidesToShow: 2,
						slidesToScroll: 1
					  }
					},
					{
					  breakpoint: 569,
					  settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					  }
					},
				  ]
			});
		}
		if($(".sf-images-slider").length) {
			$('.sf-images-slider').slick({
				dots: true,
				arrows: false,
				infinite: true,
  				slidesToShow: 3,
  				slidesToScroll: 1,
				autoplay: true,
				centerMode: true,
 				variableWidth: true,
  				autoplaySpeed: 2000,
				pauseOnHover:false
			});
		}

		/* Animation with Waypoints
		================================================== */
		if( !screencheck(767) ) {
			$('.sf-animated-row').each(function(){
				var $this = $(this);
				$this.find('.animate').each(function(i){
					var $item = $(this);
					var animation = $item.data("animate");
					$item.waypoint(function(){
							setTimeout(function () {
									$item.addClass('animated '+animation).removeClass('animate');
							}, i*150);
					},
					{
							offset: '100%',
							triggerOnce: true
					});
				});
			});
		} else {
			$('.sf-animated-row').find('.animate').removeClass('animate');
		}


/*--------------------------------------------------------------------------------------------------------------------------------------*/
	});

/*All function nned to define here for use strict mode
----------------------------------------------------------------------------------------------------------------------------------------*/



/*--------------------------------------------------------------------------------------------------------------------------------------*/
})(jQuery, window, document);
