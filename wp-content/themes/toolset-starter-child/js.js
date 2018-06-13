//production
jQuery(document).ready(function($) {


  /*******************************************************/
  /**header**/
  /*******************************************************/
  jQuery('.sf-navigation-dropdown-toggle').click(function() {
    jQuery('.sf-navigation-dropdown').toggleClass('show');
  });


  /** burger menu **/
  jQuery(function() {
    'use strict';

    // Assign
    var burgerElem = jQuery('#sf-navigation-burger');
    var navigationLinks = jQuery('.sf-navigation-item');
    var accountMenu = jQuery('#sf-navigation-account');
    var accountItems = jQuery('#sf-navigation-account-items');

    // Events
    burgerElem.off('click').on('click', burgerClicked);
    navigationLinks.off('click').on('click', navigationLinkClicked);
    accountMenu.off('click').on('click', accountMenuClicked);

    // Elements
    var burger = {
      open: false,
      elem: burgerElem
    };

    var account = {
      open: false,
      items: accountItems
    };

    // Event Functions
    function burgerClicked() {
      toggleMobileMenu();
    }

    function accountMenuClicked() {
      account.items.toggleClass('sf-navigation-items-menu-open');
      account.open = !account.open;
    }

    function navigationLinkClicked() {
      if (burger.open) {
        toggleMobileMenu();
      }
    }

    // Helper Functions
    function toggleMobileMenu() {
      burger.elem.toggleClass('open');
      burger.open = !burger.open;
      navigationLinks.toggleClass('sf-hidden');
    }

  });


  /*******************************************************/
  //Terms
  /*******************************************************/
  jQuery('.sfc-terms-filter-list li').click(function() {
    jQuery('.sfc-terms-filter-list li').removeClass('sfc-terms-active');
    jQuery(this).addClass('sfc-terms-active');
  });


  /*******************************************************/
  //FAQ
  /*******************************************************/
  var faqsAccordion = function() {

    var faqAcc = jQuery('.sfc-faq-accordion h3, .sfc-checkout-billing-faq-accordion h5');

    // Click
    faqAcc.on('click', function(event) {
      var $this = jQuery(this);

      jQuery('.sfc-faq-accordion, .sfc-checkout-billing-faq-accordion').removeClass('active');
      jQuery('.sfc-faq-accordion, .sfc-checkout-billing-faq-accordion').find('.sfc-faq-body').slideUp(400, 'easeInOutExpo');

      if (!$this.closest('.sfc-faq-accordion, .sfc-checkout-billing-faq-accordion').find('.sfc-faq-body').is(':visible')) {
        $this.closest('.sfc-faq-accordion, .sfc-checkout-billing-faq-accordion').addClass('active');
        $this.closest('.sfc-faq-accordion, .sfc-checkout-billing-faq-accordion').find('.sfc-faq-body').slideDown(400, 'easeInOutExpo');
      } else {
        $this.closest('.sfc-faq-accordion, .sfc-checkout-billing-faq-accordion').removeClass('active');
        $this.closest('.sfc-faq-accordion, .sfc-checkout-billing-faq-accordion').find('.sfc-faq-body').slideUp(400, 'easeInOutExpo');
      }


      setTimeout(function() {
        jQuery('html, body').animate({
          scrollTop: $this.closest('.sfc-faq-accordion.active, .sfc-checkout-billing-faq-accordion.active').offset().top - 90
        }, 500);
      }, 700);


      event.preventDefault();
      return false;

    });

  };

  jQuery.extend(jQuery.easing, {
    easeInOutExpo: function(x, t, b, c, d) {
      if (t == 0) return b;
      if (t == d) return b + c;
      if ((t /= d / 2) < 1) return c / 2 * Math.pow(2, 10 * (t - 1)) + b;
      return c / 2 * (-Math.pow(2, -10 * --t) + 2) + b;
    }
  });

  jQuery(function() {
    faqsAccordion();
  });



  /*******************************************************/
  //frontpage slider
  /*******************************************************/

  if (jQuery('.sfc-front-page-slider').length) {
    jQuery('.sfc-front-page-slider').owlCarousel({
      loop: true,
      items: 1,
      nav: true,
      dots: true,
      autoplay: true,
      smartSpeed: 600,
      autoplayTimeout: 88000,
      mouseDrag: false,
      animateOut: 'fadeOut',
      animateIn: 'fadeIn',
      responsive: {
        0: {
          nav: false
        },
        1100: {
          nav: true
        }
      }
    });
  }



  /*******************************************************/
  //frontpage content
  /*******************************************************/








  /*******************************************************/
  //campaign form
  /*******************************************************/


  if (jQuery('.single-product div').hasClass('alert-success')) {
    toastr.success('', 'Campaign saved');
  }



  jQuery('input[name="wpcf-collaborator-1"]').change(function() {
    jQuery('.sfc-campaign-keyup-1').val(jQuery(this).val());
  });
  jQuery('input[name="wpcf-collaborator-2"]').change(function() {
    jQuery('.sfc-campaign-keyup-2').val(jQuery(this).val());
  });





  jQuery('.sfc-campaign-yt-preview1, .sfc-campaign-channel-logo-wrapper-1').mouseenter(function() {
    if (jQuery('input[name="wpcf-youtube-video-collaborator-1"]').val() === "") {
      jQuery('.sfc-campaign-new-popover-1').css({
        'display': 'block',
        '-webkit-animation': 'fade-in .3s linear 1',
        '-moz-animation': 'fade-in .3s linear 1',
        '-ms-animation': 'fade-in .3s linear 1'
      });
    }
  });

  jQuery('.sfc-campaign-yt-preview2, .sfc-campaign-channel-logo-wrapper-2').mouseenter(function() {
    if (jQuery('input[name="wpcf-youtube-video-collaborator-2"]').val() === "") {
      jQuery('.sfc-campaign-new-popover-2').css({
        'display': 'block',
        '-webkit-animation': 'fade-in .3s linear 1',
        '-moz-animation': 'fade-in .3s linear 1',
        '-ms-animation': 'fade-in .3s linear 1'
      });
    }
  });


  jQuery('.sfc-campaign-yt-preview1, .sfc-campaign-yt-preview2, .sfc-campaign-channel-logo-wrapper-1, .sfc-campaign-channel-logo-wrapper-2').mouseleave(function() {
    jQuery('.sfc-campaign-new-popover-1, .sfc-campaign-new-popover-2').css({
      'display': 'none'
    });
  });


  jQuery('textarea[name="post_content_substitute"]').attr('onmouseover', 'this.focus();this.select()');


  jQuery('select.sfc-campaign-new-select').find('option[value="pending"]').attr('selected', true);





  jQuery('.page-campaign-form #lbl_generic.wpt-form-error.alert.alert-danger br, .page-campaign-form-edit #lbl_generic.wpt-form-error.alert.alert-danger br').find('br').remove();
  jQuery('.page-campaign-form #lbl_generic.wpt-form-error.alert.alert-danger:first-line, .page-campaign-form-edit #lbl_generic.wpt-form-error.alert.alert-danger:first-line').remove();
  jQuery('.page-campaign-form #lbl_generic.wpt-form-error.alert.alert-danger ul li:last-child, .page-campaign-form-edit #lbl_generic.wpt-form-error.alert.alert-danger ul li:last-child').addClass('sfc-error-tell-us-more');

  jQuery(function() {
    jQuery('.page-campaign-form #lbl_generic.wpt-form-error.alert.alert-danger ul li, .page-campaign-form-edit .page-campaign-form #lbl_generic.wpt-form-error.alert.alert-danger ul li').each(function() {
      var oldPhrase1 = jQuery(this).text();
      var newPhrase1 = oldPhrase1.replace('Description_substitute:', '');
      jQuery(this).text(newPhrase1);
    });
  });

  jQuery(function() {
    jQuery('li.sfc-error-tell-us-more').each(function() {
      var oldPhrase1 = jQuery(this).text();
      var newPhrase1 = oldPhrase1.replace('Description_substitute:', '');
      jQuery(this).text(newPhrase1);
    });
  });





  //minimum character count
  jQuery('textarea[name="post_content_substitute"]').keyup(function() {
    var max = 250;
    var len = jQuery(this).val().length;
    if (len >= max) {
      jQuery('#charNum').text(' you have reached the minimum');
    } else {
      var char = len;
      jQuery('#charNum').text(char + ' characters added (min. 250)');
    }
  });





  //Preview Youtube Video +
  var videobox = document.getElementById('sfc-campaign-new-videobox-1');
  var videoext = jQuery('[name="wpcf-youtube-video-collaborator-1"]').val();

  // alert(videoext);
  if (videoext) {
    jQuery('.sfc-campaign-new-youtube-video-1').hide();
    if (jQuery('[name="wpcf-youtube-video-collaborator-1"]').val() === "") {
      videobox.style.display = 'none';
    } else {

      var url = jQuery(".videourl1").val();
      $.ajax({
        url: '/wp-content/themes/toolset-starter-child/process.php',
        type: 'POST',
        data: {
          url: url
        },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          jQuery("#logo1").attr('src', data.url);
          jQuery(".imageurl1").val(data.url);
          jQuery("#sfc-campaign-new-video-preview-1").html('<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + data.videoid + '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
          jQuery("[name='wpcf-youtube-video-collaborator-1']").val('https://www.youtube.com/watch?v=' + data.videoid);
          jQuery('#sfc-campaign-new-videobox-1').css('display', 'block');
          jQuery('.sfc-campaign-yt-preview1').css('display', 'flex').fadeIn("slow");
        }
      });



      var url = jQuery('[name="wpcf-youtube-video-collaborator-1"]').val();
      var ifrm = document.createElement('iframe');
      ifrm.src = (!url.includes('vimeo')) ? "//www.youtube.com/embed/" + url.split("=")[1] : "//player.vimeo.com/video/" + url.split("/")[3];
      ifrm.width = '560';
      ifrm.height = '560';
      ifrm.frameborder = '0';
      ifrm.scrolling = 'no';
      jQuery('#sfc-campaign-new-video-preview-1').html(ifrm);
      videobox.style.display = 'block';
    }
  }








  var videobox2 = document.getElementById('sfc-campaign-new-videobox-2');
  var videoext2 = jQuery('[name="wpcf-youtube-video-collaborator-2"]').val();



  // alert(videoext2);
  if (videoext2) {
    jQuery('.sfc-campaign-new-youtube-video-2').hide();

    if (jQuery('[name="wpcf-youtube-video-collaborator-2"]').val() === "") {
      videobox2.style.display = "none";
    } else {

      var url = jQuery(".videourl2").val();
      $.ajax({
        url: '/wp-content/themes/toolset-starter-child/process.php',
        type: 'POST',
        data: {
          url: url
        },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          jQuery("#logo2").attr('src', data.url);
          jQuery(".imageurl2").val(data.url);
          jQuery("#sfc-campaign-new-video-preview-2").html('<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + data.videoid + '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
          jQuery("[name='wpcf-youtube-video-collaborator-2']").val('https://www.youtube.com/watch?v=' + data.videoid);
          jQuery('#sfc-campaign-new-videobox-2').css('display', 'block');
          jQuery('.sfc-campaign-yt-preview2').css('display', 'flex').fadeIn("slow");
        }
      });


      var url = jQuery('[name="wpcf-youtube-video-collaborator-2"]').val();
      var ifrm = document.createElement('iframe');
      ifrm.src = (!url.includes('vimeo')) ? "//www.youtube.com/embed/" + url.split("=")[1] : "//player.vimeo.com/video/" + url.split("/")[3];
      ifrm.width = "560";
      ifrm.height = "560";
      ifrm.frameborder = "0";
      ifrm.scrolling = "no";
      jQuery('#sfc-campaign-new-video-preview-2').html(ifrm);
      videobox2.style.display = "block";
    }
  }


  jQuery(".videourl1").on('paste', function(e) {
    jQuery('.sfc-campaign-new-youtube-video-1').hide();
    jQuery('.sfc-campaign-collaborator-1-upload-image .js-wpt-field-items .wpt-credfile-preview .wpt-credfile-delete').click();
    setTimeout(function() {
      console.log('Pasted');
      var url = jQuery(".videourl1").val();
      $.ajax({
        url: '/wp-content/themes/toolset-starter-child/process.php',
        type: 'POST',
        data: {
          url: url
        },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          jQuery("#logo1").attr('src', data.url);
          jQuery(".imageurl1").val(data.url);
          jQuery("#sfc-campaign-new-video-preview-1").html('<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + data.videoid + '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
          jQuery("[name='wpcf-youtube-video-collaborator-1']").val('https://www.youtube.com/watch?v=' + data.videoid);
          jQuery('#sfc-campaign-new-videobox-1').css('display', 'block');
          jQuery('.sfc-campaign-yt-preview1').css('display', 'flex').fadeIn("slow");
        }
      });
    }, 100);
  });


  jQuery(".videourl2").on('paste', function(e) {
    jQuery('.sfc-campaign-collaborator-2-upload-image .js-wpt-field-items .wpt-credfile-preview .wpt-credfile-delete').click();
    jQuery('.sfc-campaign-new-youtube-video-2').hide();
    setTimeout(function() {
      console.log('Pasted');
      var url = jQuery(".videourl2").val();
      $.ajax({
        url: '/wp-content/themes/toolset-starter-child/process.php',
        type: 'POST',
        data: {
          url: url
        },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          jQuery("#logo2").attr('src', data.url);
          jQuery(".imageurl2").val(data.url);
          jQuery("#sfc-campaign-new-video-preview-2").html('<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + data.videoid + '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
          jQuery("[name='wpcf-youtube-video-collaborator-2']").val('https://www.youtube.com/watch?v=' + data.videoid);
          jQuery('#sfc-campaign-new-videobox-2').css('display', 'block');
          jQuery('.sfc-campaign-yt-preview2').css('display', 'flex').fadeIn("slow");
        }
      });
    }, 100);
  });




  //autocomplete

  jQuery("[name='wpcf-collaborator-1'], [name='wpcf-collaborator-2'], .sfc-campaign-archive-search-input").autocomplete({
    source: function(request, response) {
      $.getJSON("https://suggestqueries.google.com/complete/search?callback=?", {
        "hl": "en", // Language
        "ds": "yt", // Restrict lookup to youtube
        "jsonp": "suggestCallBack", // jsonp callback function name
        "q": request.term, // query term
        "client": "youtube" // force youtube style response, i.e. jsonp
      });
      suggestCallBack = function(data) {
        var suggestions = [];
        $.each(data[1], function(key, val) {
          suggestions.push({
            "value": val[0]
          });
        });
        suggestions.length = 5; // prune suggestions list to only 5 items
        response(suggestions);
      };
    },
  });



  //errors
  jQuery('#cred_form_98_1, #cred_form_216_1').bind('invalid-form.validate', function() {
    toastr.warning('', 'Error!');
    /*var TopPosition = jQuery('.page-campaign-form').offset().top;
    jQuery('html, body').animate({scrollTop:TopPosition}, 'slow');*/
  });


//success

if (jQuery('body').hasClass('page-campaign-form')) {

jQuery('[name="wpcf-collaborator-1"], [name="wpcf-collaborator-2"]').removeAttr('required');

/*jQuery( '#cred_form_98_1' ).on( 'ajaxSuccess', function() {
  alert(1234);
  var TopPosition = jQuery('.page-campaign-form').offset().top;
  jQuery('html, body').animate({scrollTop:TopPosition}, 'slow');
});*/


/*jQuery("#cred_form_98_1").on('DOMNodeInserted', function(e) {
    if ( $(e.target).hasClass('wpt-form-error') ) {
        $(e.target).addClass("classname");
    }
});*/

}

  //select taxonomy
  jQuery('.page-campaign-form select[name="product_cat[]"], .page-campaign-form-edit select[name="product_cat[]"], .page-campaign-form-edit select[name="alt_product_cat"]').prepend('<option value="">- Select a Category-</option>');
  jQuery('.page-campaign-form select[name="alt_product_cat"] option[value=""], .page-campaign-form-edit select[name="alt_product_cat"] option[value=""]').remove();
  jQuery('.page-campaign-form select[name="alt_product_cat"], .page-campaign-form-edit select[name="alt_product_cat"]').prepend('<option value="">- Select a Category-</option>');
  jQuery('.page-campaign-form select[name="product_cat[]"] option[value=""], .page-campaign-form select[name="alt_product_cat"] option[value=""], .page-campaign-form-edit select[name="product_cat[]"] option[value=""], .page-campaign-form-edit select[name="alt_product_cat"] option[value=""]').attr('selected', 'selected');



  //choose collaboration type


  jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='1']:checked + .wpt-form-radio-label").hover(function() {
    jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='1']:checked + .wpt-form-radio-label").after('<p class="sfc-campaign-collaboration-type-popover">Let the YouTubers choose the type of collaboration</p>').fadeIn(800);
  }, function() {
    jQuery('p.sfc-campaign-collaboration-type-popover').remove();
  });

  jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='2'] + .wpt-form-radio-label").hover(function() {
    jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='2'] + .wpt-form-radio-label").after('<p class="sfc-campaign-collaboration-type-popover">One YouTuber will appear as a guest on the other YouTubers channel</p>').fadeIn(800);
  }, function() {
    jQuery('p.sfc-campaign-collaboration-type-popover').remove();
  });

  jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='3'] + .wpt-form-radio-label").hover(function() {
    jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='3'] + .wpt-form-radio-label").after('<p class="sfc-campaign-collaboration-type-popover">This means a fight or a battle. I.e.: disstrack, boxing fight, basketball game etc. </p>').fadeIn(800);
  }, function() {
    jQuery('p.sfc-campaign-collaboration-type-popover').remove();
  });

  jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='4'] + .wpt-form-radio-label").hover(function() {
    jQuery(".wpt-form-set-radios-wpcf-collaboration-type input[name='wpcf-collaboration-type'][value='4'] + .wpt-form-radio-label").after('<p class="sfc-campaign-collaboration-type-popover">A popular YouTuber will introduce an upcoming YouTuber to his fanbase</p>').fadeIn(800);
  }, function() {
    jQuery('p.sfc-campaign-collaboration-type-popover').remove();
  });


  /*******************************************************/
  //campaign search
  /*******************************************************/


  jQuery('.sfc-campaign-archive-select-genre').click(function() {
    jQuery('input.select2-search__field').select();
  });




  jQuery( document ).on( 'js_event_wpv_pagination_completed', function( event, data ) {
	/**
	* data.view_unique_id (string) The View unique ID hash
	* data.effect (string) The View AJAX pagination effect
	* data.speed (integer) The View AJAX pagination speed in miliseconds
	* data.layout (object) The jQuery object for the View layout wrapper
	*/
	  var div, n,
            v = document.querySelectorAll(".sf-campaign-yt-video-thumbnail");
        for (n = 0; n < v.length; n++) {
            div = document.createElement("div");
            div.setAttribute("data-id", v[n].dataset.id);
            div.innerHTML = Thumb(v[n].dataset.id);
            div.onclick = Iframe;
            v[n].appendChild(div);
        }
});




jQuery( document ).on( 'js_event_wpv_parametric_search_triggered', function( event, data ) {

}); //js_event_wpv_parametric_search_triggered

jQuery( document ).on( 'js_event_wpv_parametric_search_started', function( event, data ) {
 var TopPosition = jQuery('.post-type-archive-product').offset().top;
 jQuery('html, body').animate({scrollTop:TopPosition}, 'slow');
}); //js_event_wpv_parametric_search_started

jQuery( document ).on( 'js_event_wpv_parametric_search_form_updated', function( event, data ) {
jQuery("select.selecttwo").select2({ placeholder: "Select a category" });
jQuery(".form-control").select2({ minimumResultsForSearch: Infinity });
}); //js_event_wpv_parametric_search_form_updated

jQuery( document ).on( 'js_event_wpv_parametric_search_results_updated', function( event, data ) {

  jQuery(".sfc-campaign-archive-search-input").autocomplete({
    source: function(request, response) {
      $.getJSON("https://suggestqueries.google.com/complete/search?callback=?", {
        "hl": "en", // Language
        "ds": "yt", // Restrict lookup to youtube
        "jsonp": "suggestCallBack", // jsonp callback function name
        "q": request.term, // query term
        "client": "youtube" // force youtube style response, i.e. jsonp
      });
      suggestCallBack = function(data) {
        var suggestions = [];
        $.each(data[1], function(key, val) {
          suggestions.push({
            "value": val[0]
          });
        });
        suggestions.length = 5; // prune suggestions list to only 5 items
        response(suggestions);
      };
    },
  });



jQuery('.select2-container.select2-container--default.select2-container--open').remove();
if (jQuery('body').hasClass('post-type-archive-product')) {
jQuery("select.selecttwo").select2({ placeholder: "Select a category" });
}
jQuery('.sfc-campaign-archive-overlay-close').click();
jQuery(".sfc-campaign-archive-reset-button").addClass("show");

jQuery('button.sfc-campaign-archive-reset-button').click(function (){
jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('sfc-campaign-archive-search-sort-item-link-active');
//jQuery('.rand').addClass('sfc-campaign-archive-search-sort-item-link-active');
  jQuery('.post_date').addClass('sfc-campaign-archive-search-sort-item-link-active');
//jQuery('input:radio[name=wpv_sort_orderby][value="rand"]').click();
  jQuery('input:radio[name=wpv_sort_orderby][value="post_date"]').click();
});

//Check if option is selected
if( jQuery('select[name=wpv-_nyp] option:selected').val() == 'no' ) {
    jQuery('._product_total_sales').addClass('hide');
    jQuery('._product_total_customers').addClass('hide');
    jQuery('._upvote_count').removeClass('hide');
  }
if( jQuery('select[name=wpv-_nyp] option:selected').val() == 'yes' ) {
    jQuery('._upvote_count').addClass('hide');
    jQuery('._product_total_sales').removeClass('hide');
    jQuery('._product_total_customers').removeClass('hide');
  }
    if( jQuery('select[name=wpv-_nyp] option:selected').val() == '' ) {
    jQuery('._upvote_count').removeClass('hide');
    jQuery('._product_total_sales').removeClass('hide');
    jQuery('._product_total_customers').removeClass('hide');
  }



        var div, n,
            v = document.querySelectorAll(".sf-campaign-yt-video-thumbnail");
        for (n = 0; n < v.length; n++) {
            div = document.createElement("div");
            div.setAttribute("data-id", v[n].dataset.id);
            div.innerHTML = Thumb(v[n].dataset.id);
            div.onclick = Iframe;
            v[n].appendChild(div);
        }



  jQuery('.sfc-campaign-archive-post-title-inner').mouseover( function() {
    if( this.offsetWidth > this.parentNode.offsetWidth ) {
        $(this).animate({'left': '-'+(this.offsetWidth)+'px'}, 4000, function(){this.style.left='0px';});
    }
} ).mouseout( function() {
    jQuery(this).stop();
    this.style.left = '0px';
} );


}); //on. js_event_wpv_parametric_search_results_updated



    jQuery(".sfc-campaign-archive-refined-search-mobile").click(function () {
        jQuery(".sfc-campaign-archive-overlay-wrapper").fadeIn('500').show();
    });
    jQuery(".sfc-campaign-archive-overlay-close").click(function () {
        jQuery(".sfc-campaign-archive-overlay-wrapper").fadeOut('500').hide();
    });
    jQuery('.sfc-campaign-archive-select-genre').click(function () {
        jQuery('.sfc-campaign-archive-select-container-open-genre').toggle();
        jQuery('.sfc-campaign-archive-select-container-open-status').css('display', 'none');
    });
    jQuery('.sfc-campaign-archive-select-status').click(function () {
        jQuery('.sfc-campaign-archive-select-container-open-status').toggle();
        jQuery('.sfc-campaign-archive-select-container-open-genre').css('display', 'none');
    });
    jQuery('.sfc-campaign-select-multiple-genre-item').click(function () {
        jQuery(this).hide();
    });




jQuery('ul li.sort-dir').fadeIn(500).appendTo('div.sfc-campaign-archive-refined-search').css('display','inline');

jQuery("span.select2-container").fadeIn(3000);
jQuery('.d').fadeIn(500).removeClass('hide');

if (jQuery('body').hasClass('post-type-archive-product')) {
jQuery(".form-control").select2({ minimumResultsForSearch: Infinity });
jQuery("select.selecttwo").select2({ placeholder: "Select a category" });
}

     jQuery('ul.sfc-campaign-archive-search-sort li a').on('click', function(){
    jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('sfc-campaign-archive-search-sort-item-link-active ');
    jQuery(this).addClass('sfc-campaign-archive-search-sort-item-link-active ');
});


/*      //Random order
       jQuery(".rand").click(function () {
         jQuery('input:radio[name=wpv_sort_orderby][value="rand"]').click(); //select radio button
});

    if( jQuery ('input:radio[name=wpv_sort_orderby][value="rand"]').is(':checked') ){
    jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('sfc-campaign-archive-search-sort-item-link-active');
  jQuery('.rand').addClass('sfc-campaign-archive-search-sort-item-link-active');
  } */



 //Date
         jQuery(".post_date").click(function () {
       jQuery('input:radio[name=wpv_sort_orderby][value="post_date"]').click(); //select radio button
});

    if( jQuery ('input:radio[name=wpv_sort_orderby][value="post_date"]').is(':checked') ){
    jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('sfc-campaign-archive-search-sort-item-link-active');
  jQuery('.post_date').addClass('sfc-campaign-archive-search-sort-item-link-active');
  }

   //Popularity
       jQuery(".field-wpcf-campaign-view-count").click(function () {
       jQuery('input:radio[name=wpv_sort_orderby][value="field-wpcf-campaign-view-count"]').click(); //select radio button
});

      if( jQuery ('input:radio[name=wpv_sort_orderby][value="field-wpcf-campaign-view-count"]').is(':checked') ){
    jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('sfc-campaign-archive-search-sort-item-link-active');
  jQuery('.field-wpcf-campaign-view-count').addClass('sfc-campaign-archive-search-sort-item-link-active');
  }


   //Upvotes
  jQuery("._upvote_count").click(function() {
    jQuery('input:radio[name=wpv_sort_orderby][value="field-_upvote_count"]').click(); //select radio button
    });

      if( jQuery ('input:radio[name=wpv_sort_orderby][value="field-_upvote_count"]').is(':checked') ){
    jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('sfc-campaign-archive-search-sort-item-link-active');
  jQuery('.field-_upvote_count').addClass('sfc-campaign-archive-search-sort-item-link-active');
  }


    //Most donations
  jQuery("._product_total_sales").click(function () {
                 jQuery('input:radio[name=wpv_sort_orderby][value="field-_product_total_sales"]').click(); //select radio button
  });

      if( jQuery ('input:radio[name=wpv_sort_orderby][value="field-_product_total_sales"]').is(':checked') ){
    jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('target');
  jQuery('.field-_product_total_sales').addClass('target');
  }


        //Most supported
  jQuery("._product_total_customers").click(function () {
                 jQuery('input:radio[name=wpv_sort_orderby][value="field-_product_total_customers"]').click(); //select radio button
  });


  if( jQuery ('input:radio[name=wpv_sort_orderby][value="field-_product_total_customers"]').is(':checked') ){
    jQuery('ul.sfc-campaign-archive-search-sort li a').removeClass('sfc-campaign-archive-search-sort-item-link-active');
  jQuery('._product_total_customers').addClass('sfc-campaign-archive-search-sort-item-link-active');
  }



  /*
  // Hide radio fields based on Campaign Status
  if( jQuery('select[name=wpv-_alg_crowdfunding_enabled] option:selected').val() == 'no' ) {
    jQuery('.field-_alg_crowdfunding_total_orders').addClass('hide');
    jQuery('.field-_alg_crowdfunding_orders_sum').addClass('hide');
    jQuery('.field-_thumbs_rating_up').removeClass('hide');
  }

if( jQuery('select[name=wpv-_alg_crowdfunding_enabled] option:selected').val() == 'yes' ) {
    jQuery('.field-_thumbs_rating_up').addClass('hide');
    jQuery('.field-_alg_crowdfunding_total_orders').removeClass('hide');
    jQuery('.field-_alg_crowdfunding_orders_sum').removeClass('hide');
  }*/

    jQuery('.js-wpv-reset-trigger').click(function() {
   jQuery('.field-_thumbs_rating_up, .field-_alg_crowdfunding_orders_sum, .field-_alg_crowdfunding_total_orders').removeClass('hide');
 });

  jQuery('.sfc-campaign-archive-overlay-reset').click(function() {
    jQuery('.js-wpv-reset-trigger').click();
  });








  /*******************************************************/
  //campaign
  /*******************************************************/

  jQuery('.single-product .sf-campaign-yt-video-thumbnail:first-child').click(function() {
    jQuery('.sf-campaign-item-inner-1').addClass('hide1');
  });
  jQuery('.single-product .sf-campaign-yt-video-thumbnail:last-child').click(function() {
    jQuery('.sf-campaign-item-inner-2').addClass('hide1');
  });


  setTimeout(function() {
    jQuery('div.at-share-btn-elements').append(' <a href="mailto:" role="button" tabindex="1" class="at-icon-wrapper1 at-share-btn1 at-svc-email1" style="background-color: rgb(132, 132, 132); border-radius: 0%;"><span class="at4-visually-hidden">Share to Email</span><span class="at-icon-wrapper1" style="line-height: 32px; height: 32px; width: 32px;"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" version="1.1" role="img" aria-labelledby="at-svg-email-15" class="at-icon at-icon-email" style="width: 32px; height: 32px;"><title id="at-svg-email-15">Email</title><g><g fill-rule="evenodd"></g><path d="M27 22.757c0 1.24-.988 2.243-2.19 2.243H7.19C5.98 25 5 23.994 5 22.757V13.67c0-.556.39-.773.855-.496l8.78 5.238c.782.467 1.95.467 2.73 0l8.78-5.238c.472-.28.855-.063.855.495v9.087z"></path><path d="M27 9.243C27 8.006 26.02 7 24.81 7H7.19C5.988 7 5 8.004 5 9.243v.465c0 .554.385 1.232.857 1.514l9.61 5.733c.267.16.8.16 1.067 0l9.61-5.733c.473-.283.856-.96.856-1.514v-.465z"></path></g></svg></span><span class="at-label" style="font-size: 11.4px; line-height: 32px; height: 32px;">Email</span></a>');
  }, 3000);


  jQuery('.upvote-container f.upvote-progress-button.icon.success').click(function() {

    toastr.warning('', 'You already voted!');

  });



  //campaign read more


  $('.sfc-description-section').each(function(index, element) {
    console.log($(this).find('.sfc-campaign-excerpt').outerHeight());
    var cont_len = $(this).find('.sfc-campaign-excerpt').height();
    if (cont_len < 55)
      $(this).find('.sfc-read-more').hide('fast');
  });



  $('.sfc-read-more').on('click', function(e) {
    if ($(this).parents('.sfc-description-section').hasClass('open')) {
      $(this).parents('.sfc-description-section').removeClass('open');
      $(this).text('..read more');
    } else {
      $(this).parents('.sfc-description-section').addClass('open');
      $(this).text('Read less');
    }
    e.preventDefault();
  });



  /*text rotation*/

  jQuery('.sfc-campaign-archive-post-title-inner').mouseover(function() {
    if (this.offsetWidth > this.parentNode.offsetWidth) {
      jQuery(this).animate({
        'left': '-' + (this.offsetWidth) + 'px'
      }, 4000, function() {
        this.style.left = '0px';
      });
    }
  }).mouseout(function() {
    jQuery(this).stop();
    this.style.left = '0px';
  });



  /*******************************************************/
  /**account**/
  /*******************************************************/




  if (jQuery('.woocommerce-MyAccount-navigation-link--my-campaigns').hasClass('is-active')) {
    jQuery('.sfc-account-mobile-nav-campaigns').addClass('sfc-account-mobile-nav-li-active');
  }
  if (jQuery('.woocommerce-MyAccount-navigation-link--orders').hasClass('is-active')) {
    jQuery('.sfc-account-mobile-nav-donations').addClass('sfc-account-mobile-nav-li-active');
  }
  if (jQuery('.woocommerce-MyAccount-navigation-link--settings, .woocommerce-MyAccount-navigation-link--edit-account').hasClass('is-active')) {
    jQuery('.sfc-account-mobile-nav-settings').addClass('sfc-account-mobile-nav-li-active');
  }






  /**account settings**/

  jQuery('span.frm_upload_text').text('Upload your profile picture');


  /**order details**/
  jQuery(function() {
    jQuery('.woocommerce-view-order .woocommerce-MyAccount-content p:first-child').each(function() {
      var oldPhrase = jQuery(this).text();
      var newPhrase = oldPhrase.replace('Order', 'Contribution');
      jQuery(this).text(newPhrase);
    });

  });


  /**campaign backers**/

  if (jQuery('ul.sfc-campaign-backers li').hasClass('sfc-campaign-backers-list')) {
    jQuery('.sfc-campaign-no-backers-found').removeClass('show');
  }


  /*******************************************************/
  //checkout**/
  /*******************************************************/


  /** checkout general **/

  if (jQuery('body.page-checkout, body.page-checkout-choose-organization').hasClass('woocommerce')) {
    jQuery('body.page-checkout .site-content, body.page-checkout-choose-organization .site-content').css('height', 'normal');
  } else {
    jQuery('body.page-checkout .site-content, body.page-checkout-choose-organization .site-content').css('height', '100vh');
  }


  jQuery("a.woo-slg-show-social-login").parent().css({
    "display": "none"
  });

  jQuery('.page-checkout .sfc-faq-body').css('display', 'none');
  jQuery('.page-checkout .sfc-checkout-billing-faq-accordion').removeClass('active');

  /** checkout add to basket **/

  jQuery('input[name="nyp"]').attr('min', '1');

  jQuery('input[name="nyp"]').attr('value', '3');

  jQuery('input[name="nyp"]').keyup(function() {
    var value = jQuery(this).val();

    if (value <= 0) {
      value = value.replace(/^(0*)/, '');
    }
    jQuery(this).val(value);
  });



  jQuery('input[name="donation_amount"], input.product_custom_price').on('change', function(e) {
    var selectedValue = jQuery(this).val();
    jQuery('input[name="nyp"], input[type="text"].product_custom_price').val(selectedValue);
    jQuery('div.nyp').removeClass('nyp-selected');
    jQuery('.sfc-checkout-billing-donation-amount').removeClass('sfc-checkout-billing-donation-amount-active');
  });
  jQuery('input[name="donation_amount"]').on('change', function() {
    jQuery('div.nyp').removeClass('nyp-selected');
  });

  jQuery('input[name="nyp"]').click(function() {
    jQuery('input[name="donation_amount"]').removeAttr('checked');
    jQuery('div.nyp').addClass('nyp-selected ');
  });

  jQuery('.sfc-campaign-cta').click(function() {
    jQuery('.sfc-campaign-choose-amount-wrapper').removeClass('checkout-hide');
    jQuery(this).addClass('checkout-hide');
  });



  /** checkout choose amount **/

    jQuery('.sfc-checkout-billing-donation-amount input[type="text"]').on('click', function() {
    jQuery('.sfc-checkout-billing-donation-amount').removeClass('sfc-checkout-billing-donation-amount-active');
    jQuery(this).parents('.sfc-checkout-billing-donation-amount').addClass('sfc-checkout-billing-donation-amount-active');
    jQuery('input.product_custom_price').attr('checked', false);
  });


  var setCookie = function(cname, cvalue) {
    var d = new Date();
    d.setTime(d.getTime() + (24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  };

  jQuery('button.sfc-checkout-add-org').on('click', function() {
    var orgid = jQuery(this).data('id');
    setCookie('vlogfundorg', orgid);
  });

  /** checkout choose organization **/

    jQuery('.page-checkout .sfc-checkout-non-clickable').removeAttr('href');
    jQuery(document).on('click', 'a button.sfc-checkout-add-org', function() {
    jQuery('a button.sfc-checkout-add-org').html('<strong class="mobile-none">Choose this cause</strong><strong class="desktop-none">Select</strong>');
    jQuery(this).html('<strong>Selected</strong>');
    jQuery('.sfc-checkout-org-not-selected').hide();

    //Make Billing Cause Selected
    var $orgid = jQuery(this).data('id');
    if (typeof $orgid !== 'undefined' && $orgid != '') {
      jQuery('#billing_cause_field').find('input[id^="billing_cause_' + $orgid + '"]').attr('checked', true);
    }
  });

  if (jQuery('.product_custom_price').length) {
    jQuery(document).on('change', 'td.product-price input[type="radio"]', function() {
      var newprice = jQuery(this).val();
      jQuery(this).parents('td.product-price').find('input[type="text"]').val(newprice).trigger('blur');
      return false;
    });

    // Init a timeout variable to be used below
    var timeout = null;
    jQuery(document).on('keyup blur', 'input[type="text"].product_custom_price', function() {
      clearTimeout(timeout);
      timeout = setTimeout(function() {
        var productprices = {};
        jQuery('.product_custom_price').each(function() {
          var offerprice = jQuery(this).val();
          var productid = jQuery(this).data('id');
          if (typeof offerprice !== 'undefined' && offerprice != '') {
            productprices[productid] = offerprice;
          }
        });
        $.ajax({
          url: Vlogfund.ajaxurl,
          method: 'POST',
          data: {
            action: 'update_product_price_on_cart',
            products: productprices
          },
          success: function(response) {
            //alert(response);
            jQuery(document.body).trigger('update_checkout');
            jQuery('.product_custom_price').parents('table').find('input[name="update_cart"]').trigger('click');
          }
        });
      }, 500);
    });
  }


  jQuery(document).on('updated_cart_totals', function() {
    jQuery("a.woo-slg-show-social-login").parent().css({
      "display": "none"
    });
    toastr.success('', 'Amount updated');
    var div, n,
      v = document.querySelectorAll(".sf-cart-yt-video-thumbnail");
    for (n = 0; n < v.length; n++) {
      div = document.createElement("div");
      div.setAttribute("data-id", v[n].dataset.id);
      div.innerHTML = Thumb(v[n].dataset.id);
      div.onclick = Iframe;
      v[n].appendChild(div);
    }
  });


  //checkout thank you

  if (jQuery('body').hasClass('woocommerce-order-received')) {
    setTimeout(function() {
      jQuery('a.thank-you-share').get(0).click();
    }, 2500);
  }


  /*******************************************************/
  /** organizations **/
  /*******************************************************/

  /** organizations @ checkout **/

  if (location.href.indexOf("#checkout") != -1) {
    jQuery('.site-header, .site-footer').hide();
  }


  /*******************************************************/
  /** login & register **/
  /*******************************************************/


  /**IOS pop up issue workaround**/

  //target ios
  var isMobile = {
      iOS: function() {
          return navigator.userAgent.match(/iPhone|iPad|iPod/i);
      }
  }

    if(isMobile.iOS()) {
      jQuery('body').css({
          'position' : 'relative',
          'width' : '100%',
          'height' : '100%',
          '-webkit-overflow-scrolling' : 'touch',
          'overflow' : 'hidden',
        });
      }


  /**popup visibility*/

  jQuery('a[href="#login"]').click(function(e) {
    jQuery('#login.sf-popup').addClass('login-popup-visible');
    jQuery('#register.sf-popup').removeClass('register-popup-visible');
    e.preventDefault();
  });

  jQuery('a[href="#register"]').click(function(e) {
    jQuery('#register.sf-popup').addClass('register-popup-visible');
    jQuery('#login.sf-popup').removeClass('login-popup-visible');
    e.preventDefault();
  });

  jQuery('a[href="#"]').click(function(e) {
    jQuery('#register.sf-popup').removeClass('register-popup-visible');
    jQuery('#login.sf-popup').removeClass('login-popup-visible');
    e.preventDefault();
  });



  /** comments **/
  jQuery('.sf-comments-logged-out').click(function() {
    jQuery('.sf-reg-li a').get(0).click();
  });




  jQuery('.page-account #rememberme').prop('checked', true);


  //perform AJAX login and registration on form submit


  // Perform AJAX login/register on form submit
  $('form#login_user, form#register_user').on('submit', function(e) {
    if (!$(this).valid()) return false;
    $('p.status', this).show().text(ajax_auth_object.loadingmessage);
    action = 'ajaxlogin';
    username = $('form#login_user #username').val();
    password = $('form#login_user #password').val();
    email = '';
    //security = $('form#login #security').val();
    if ($(this).attr('id') == 'register_user') {
      action = 'ajaxregister';
      username = $('#signonname').val();
      password = $('#signonpassword').val();
      email = $('#email').val();
      //security = $('#signonsecurity').val();
    }
    ctrl = $(this);
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: ajax_auth_object.ajaxurl,
      data: {
        'action': action,
        'username': username,
        'password': password,
        'email': email,
        //'security': security
      },
      success: function(data) {
        $('p.status', ctrl).text(data.message);
        /*$('.login-w-a').addClass('hide');
        $('li.account-li').removeClass('hide');
        toastr.success('', 'Welcome!');*/
        /*if ($(this).attr('id') == 'register_user') {
          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({
          event: 'formSubmissionSuccess',
          formName: 'registerForm'
          });
            toastr.success('', 'Welcome!');
          } else {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
            event: 'formSubmissionSuccess',
            formName: 'loginForm'
            });
              toastr.success('', 'Welcome back!');
            }*/
        if (data.loggedin == true) {
          toastr.success('', 'Welcome!');
          if ($('body').hasClass('single-post')) {
            $('span.sf-comment-register a.sf-comment-register-redirect').get(0).click();
          } else {
            //setTimeout(function() {   document.location.href = ajax_auth_object.redirecturl; },500);
            window.location.reload(true);
          }

        }
      }
    });
    e.preventDefault();
  });


  // Client side form validation
  if (jQuery("#register_user").length)
    jQuery("#register_user").validate({
      rules: {
        password2: {
          equalTo: '#signonpassword'
        }
      }
    });
  else if (jQuery("#login_user").length)
    jQuery("#login_user").validate();



  //login with email toggle

    jQuery('.sf-email-login, .sf-email-registration').click(function() {
    jQuery('.sf-email-login-form, .sf-email-registration-form').removeClass('hide');
    jQuery('.sf-email-login, .sf-email-registration').hide();
  });

  /*******************************************************/
  //Blog
  /*******************************************************/



  //nl sign up

  jQuery(window).load(function() {

    jQuery('.sf-mc-button').on("click", function(event) {

      var $input = jQuery(this).parent().find('input');
      not_empty = ('' !== $input.val()) ? true : false;

      setTimeout(function() {
        event.preventDefault();
        var m_text = jQuery('#subscribe-result p').text();
        if (m_text == 'Thank you for subscribing. We have sent you a confirmation email.' && true === not_empty) {

          window.dataLayer = window.dataLayer || [];
          dataLayer.push({
            'event': 'formSubmitted',
            'formName': 'Newsletter Widget'
          });

        }
      }, 2000);

    });

  });


  /**** Smile Switch ****/
  if (jQuery('.sf-smile-switch').length) {
    jQuery('.sf-smile-switch input[type="checkbox"]').on('change', function() {
      setTimeout(function() {
        jQuery('html,body').animate({
          scrollTop: 0
        }, 650);
      }, 500);
      if (jQuery(this).is(':checked')) {
        setCookie('vlogfundsmilemode', 1);
        toastr.success('', 'Smile mode activated');
      } else {
        setCookie('vlogfundsmilemode', 0);
        toastr.success('', 'Smile mode deactivated');
      }
      location.reload(true);
    });
  }




}); //end





/*******************************************************/
//Comments
/*******************************************************/

//remove comment author last name

jQuery('span.decomments-autor-name').html(function() {
  // separate the text by spaces
  var text = jQuery(this).text().split(' ');
  // drop the last word and store it in a variable
  var last = text.pop();
  // join the text back and if it has more than 1 word add the span tag
  // to the last word
  return text.join(" ") + (text.length > 0 ? ' <span class="lastname-comment">' + last + '</span>' : last);
});


//replace author with creator

jQuery(function() {
  jQuery('span.decomments-author').each(function() {
    var oldPhrase3 = jQuery(this).text();
    var newPhrase3 = oldPhrase3.replace('Author', 'Creator');
    jQuery(this).text(newPhrase3);
  });
});




/*******************************************************/
//Thumbnails
/*******************************************************/



document.addEventListener("DOMContentLoaded",
  function() {
    var div, n,
      v = document.querySelectorAll(".sf-post-yt-video-thumbnail,.sf-blog-yt-video-thumbnail,.sf-blog-yt-video-thumbnail-single,.sf-blog-yt-video-thumbnail-trending, .sf-blog-yt-video-thumbnail-top, .sf-campaign-yt-video-thumbnail, .sf-campaign-popular-yt-video-thumbnail, .sf-cart-yt-video-thumbnail, .youtube-player-blog");
    for (n = 0; n < v.length; n++) {
      div = document.createElement("div");
      div.setAttribute("data-id", v[n].dataset.id);
      div.innerHTML = Thumb(v[n].dataset.id);
      div.onclick = Iframe;
      v[n].appendChild(div);
    }
  });

function Thumb(id) {
  var thumb = '<img src="https://i.ytimg.com/vi/ID/hqdefault.jpg">',
    //var thumb = '<img src="https://i.ytimg.com/vi/ID/maxresdefault.jpg">',
    play = '<div class="sf-play"></div>';
  return thumb.replace("ID", id) + play;
}

function Iframe() {
  var iframe = document.createElement("iframe");
  var embed = "https://www.youtube.com/embed/ID?autoplay=1";
  iframe.setAttribute("src", embed.replace("ID", this.dataset.id));
  iframe.setAttribute("frameborder", "0");
  iframe.setAttribute("allowfullscreen", "1");
  this.parentNode.replaceChild(iframe, this);
}
