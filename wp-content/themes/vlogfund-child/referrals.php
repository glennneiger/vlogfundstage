<?php
/**
 * Template Name: Vlogfund Referrals
 */
?>
<?php wp_head(); ?>
<?php get_template_part(header); ?>

<style>
* { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -o-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box; }
*:before, *:after { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -o-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box; }

html, body { -webkit-font-smoothing:antialiased; -moz-font-smoothing:antialiased; -ms-font-smoothing:antialiased; font-smoothing:antialiased; /* Fix for webkit rendering */ -webkit-text-size-adjust:100%; margin:0; padding:0; }


.sfc-campaign-archive-post .sfc-campaign-images {
  margin-top:0;
}

.sfc-campaign-archive-post .sfc-campaign-image {
  height: 150px;
}


.sfc-campaign-archive-posts {
  height: auto;
}

@media screen and (max-width: 767px) {
.sfc-campaign-archive-posts {
  white-space: nowrap;
overflow-y: hidden;
overflow-x: auto;
-webkit-overflow-scrolling: touch;
width: 100%!important;
margin-left: 0px;
margin-right: 0px;
padding-top: 5px;
display: block!important;
height: auto;
}

.sfc-campaign-archive-post {
  display: inline-block;
      width: 90%;
      /* padding-right: 0px; */
      float: none;
      vertical-align: top;
      position: relative;
      /*padding: 0;*/
}


.sfc-campaign-archive-post:first-child {
  margin-left: 0;
}

.sfc-campaign-archive-post:last-child {
  margin-right: 0;
}

}

.sfc-campaign-make-it-happen-vote {
  margin-bottom: 0!important;
}

.sfc-campaign-archive-post-excerpt {
  white-space:initial;
}
</style>

<div class="vf-ref-referrals-promotions">
  <div class="vf-ref-promotions-top-banner">
    <div class="vf-ref-promotions-img-col">
    <img class="vf-ref-vf-ref-banner-hero" src="/wp-content/themes/vlogfund-child/images/vf-ref-hero-web.png" />
  </div>
    <div class="vf-ref-promotions-content">
      <div class="vf-ref-promotions-content-inner">
        <h1><span>Become a</span> <strong>Vlogfund Ambassador</strong></h1>
        <p>Decide which YouTube Collaborations will become tomorrows reality. Refer one of the campaigns down below and win awesome prizes</p>
        <a href="#main-scroll" class="vf-ref-campaign-btn">Let's get it</a>
      </div>
    </div>
  </div>

    <div class="vf-ref-how-it-works" id="main-scroll">
    <div class="vf-ref-wrap">
      <h2>How It Works</h2>
      <div class="vf-ref-work-steps">
        <div class="vf-ref-work-step">
          <span class="vf-ref-step-number">1</span>
          Upvote the YouTube collaboration you are cheering for
        </div>
        <div class="vf-ref-work-step">
          <span class="vf-ref-step-number">2</span>
          Share the collaboration campaign and get your friends and family to upvote
        </div>
        <div class="vf-ref-work-step">
          <span class="vf-ref-step-number">3</span>
          Win awesome prizes if you get the most people to "upvote" your dream collaboration
        </div>
      </div>
    </div>
  </div>


    <div class="vf-ref-campaigns">
      <div class="vf-ref-wrap">
        <h2>Get 'Em To Collab With Your Voice</h2>
        <?php echo do_shortcode('[wpv-view name="campaign-search" view_display="layout" orderby="field-_upvote_count" order="desc" limit="4" referral="1"]');?>
      </div>
    </div>





  <div class="vf-ref-prizes-section">
    <div class="vf-ref-wrap">
      <h2>The Prizes You Could Win</h2>
      <p><em>Win some of these awesome prizes by referring your friends & family</em></p>
      <div class="vf-ref-grid-cols3 price-cols">
        <div class="vf-ref-grid-col price-col">
          <figure><img class="vf-ref-price-img vf-ref-price-img-1" src="/wp-content/themes/vlogfund-child/images/vf-ref-tshirt-web.png" alt=""></figure>
          <h4>Free Merchandise</h4>
        </div>
        <div class="vf-ref-grid-col price-col">
          <figure><img class="vf-ref-price-img vf-ref-price-img-2" src="/wp-content/themes/vlogfund-child/images/vf-ref-tickets-web.png" alt=""></figure>
          <h4>Event Tickets</h4>
        </div>
        <div class="vf-ref-grid-col price-col">
          <figure><img class="vf-ref-price-img vf-ref-price-img-3" src="/wp-content/themes/vlogfund-child/images/vf-ref-subscriptions-web.png" alt=""></figure>
          <h4>Free Subscriptions</h4>
        </div>
        <div class="vf-ref-grid-col price-col">
          <figure><img class="vf-ref-price-img vf-ref-price-img-4" src="/wp-content/themes/vlogfund-child/images/vf-ref-art-web.png" alt=""></figure>
          <h4>Fanart</h4>
        </div>
        <div class="vf-ref-grid-col price-col">
          <figure><img class="vf-ref-price-img vf-ref-price-img-5" src="/wp-content/themes/vlogfund-child/images/vf-ref-games-web.png" alt=""></figure>
          <h4>Books & Games</h4>
        </div>
        <div class="vf-ref-grid-col price-col">
          <figure><i style="color:#dc2c5d;font-size:120px;" class="fa fa-heart"></i></figure><br>
          <h4>Karma</h4>
        </div>
      </div>
    </div>
  </div>



  <div class="vf-ref-faq-section">
    <div class="vf-ref-wrap">
      <div class="vf-ref-grid-cols2 faq-cols">
        <div class="vf-ref-grid-col faq-col">
          <figure class="vf-ref-faq-img"><img src="/wp-content/themes/vlogfund-child/images/question-bubble.svg" alt=""></figure>
        </div>
        <div class="vf-ref-grid-col faq-col">
          <div class="vf-ref-faq-box">
            <h3>Frequently asked questions</h3>
            <div class="vf-ref-accordion-databox">
              <div class="vf-ref-accordion-row">
                <h5 class="vf-ref-accordion-trigger">What is Vlogfund? <span class="vf-ref-accordion-trigger-icon"></span></h5>
                <div class="vf-ref-accordion-data">
                  <p>Vlogfund is a crowdsourcing platform for YouTube collaborations that opens the door to a world, where music fans decide which YouTube collaborations will become tomorrow’s reality. Together, as a YouTube community, we use Vlogfund as a tool to convince YouTubers to make collaboration requests come true.</p>
                </div><!--/.accordion-data -->
              </div><!--/.accordion-row -->
              <div class="vf-ref-accordion-row">
                <h5 class="vf-ref-accordion-trigger">What does it mean to “upvote”? <span class="vf-ref-accordion-trigger-icon"></span></h5>
                <div class="vf-ref-accordion-data">
                  <p>On Vlogfund you can support collaborations with your vote. Votes express interest in the realization of the collaboration. The more people that vote for a collaboration campaign, the stronger the appeal to the YouTubers, and the higher the chance for the realization of the collaboration. Your vote can become an integral part of the history of the collaboration when it is realized.</p>
                </div><!--/.accordion-data -->
              </div><!--/.accordion-row -->
            </div><!--/.accordion-databox -->
            <p class="vf-ref-legal-notice"><strong>Notice:</strong> Vlogfund can’t guarantee new YouTube collaborations. All collaboration ideas you see on Vlogfund are user-generated and need a strong community to bring them to life.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="vf-ref-campaign-cta">
    <div class="vf-ref-wrap">
      <p>A way for every fan to control which YouTube collaborations will become tomorrow’s reality.</p>
      <a href="#" class="vf-ref-campaign-btn">Refer campaigns now</a>
    </div>
  </div> -->

  <div class="vf-ref-sharing-section">
    <div class="vf-ref-wrap">

      <div><h2 class="vf-ref-sharing-title">Sharing is caring</h2>
      <div class="vf-ref-sharing-info">
        <p>Sharing on Facebook can increase upvotes by <span>3x</span></p>
      </div>
    </div>

      <figure class="vf-ref-sharing-img"><img style="width:100%;" src="/wp-content/themes/vlogfund-child/images/vf-ref-heads-web.png" alt=""></figure>
    </div>

  </div>


</div>
<?php get_template_part(footer); ?>
<script>
/* Custom General jQuery
/*--------------------------------------------------------------------------------------------------------------------------------------*/
;(function($, window, document, undefined) {
	//Genaral Global variables
	"use strict";

	$(document).ready(function() {


    /*Anchor JS */

    document.querySelectorAll('a[href^="#main-scroll"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
    e.preventDefault();

    document.querySelector(this.getAttribute('href')).scrollIntoView({
      behavior: 'smooth',
      block: "start",
      inline: "nearest"
    });
  });
});

		/* Accordion box JS
		---------------------------------------------------------------------*/
		$('.vf-ref-accordion-databox').each(function() {
			var $accordion = $(this),
			$accordionTrigger = $accordion.find('.vf-ref-accordion-trigger'),
			$accordionDatabox = $accordion.find('.vf-ref-accordion-data');

			$accordionTrigger.first().addClass('vf-ref-open');
			$accordionDatabox.first().show();

			$accordionTrigger.on('click',function (e) {
				var $this = $(this);
				var $accordionData = $this.next('.vf-ref-accordion-data');
				if( $accordionData.is($accordionDatabox) &&  $accordionData.is(':visible') ){
					$this.removeClass('vf-ref-open');
					$accordionData.slideUp(400);
					e.preventDefault();
				} else {
					$accordionTrigger.removeClass('vf-ref-open');
					$this.addClass('vf-ref-open');
					$accordionDatabox.slideUp(400);
					$accordionData.slideDown(400);
				}
			});
		});
	});

/*--------------------------------------------------------------------------------------------------------------------------------------*/
})(jQuery, window, document);
</script>
<?php wp_footer(); ?>
