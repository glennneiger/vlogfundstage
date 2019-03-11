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


.vf-ref-wrap { max-width: 1330px; width: 100%; margin-left: auto; margin-right: auto; padding-left: 25px; padding-right: 25px; }

.vf-ref-grid-cols2 {
	margin:-20px;
	display: flex;
	flex-wrap: wrap;
}
.vf-ref-grid-col {
	flex: 0 0 50%;
	max-width: 50%;
	width:100%;
	min-height:1px;
	padding:20px;
}
.vf-ref-grid-cols3 {
	margin:-15px;
	display: flex;
	flex-wrap: wrap;
}
.vf-ref-grid-cols3 .vf-ref-grid-col {
	padding:15px;
	flex: 0 0 33.333%;
	max-width: 33.333%;
	width:100%;
	min-height:1px;
}


.vf-ref-referrals-promotions {
	font-family: -apple-system, BlinkMacSystemFont, "“Segoe UI”", Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
	color: #2a2a2a;
	overflow: hidden;
}
.vf-ref-referrals-promotions h2 {
	font-size: 56px;
	/*font-weight: 900;*/
  text-align: center;
}
.vf-ref-how-it-works {
	color: #202020;
	padding: 90px 0;
	text-align: center;
	/*background: rgb(253,49,98);
	background: -moz-linear-gradient(top, rgba(253,49,98,1) 0%, rgba(249,97,74,1) 100%);
	background: -webkit-linear-gradient(top, rgba(253,49,98,1) 0%,rgba(249,97,74,1) 100%);
	background: linear-gradient(to bottom, rgba(253,49,98,1) 0%,rgba(249,97,74,1) 100%);
	filter: progid:DXImageTransform.vf-ref-Microsoft.vf-ref-gradient( startColorstr='#fd3162', endColorstr='#f9614a',GradientType=0 );*/
}
.vf-ref-work-steps {
	display: flex;
	flex-wrap: wrap;
	border-right: 1px solid rgba(55,55,55,0.15);
	margin-top: 120px;
}
.vf-ref-work-step {
	flex: 0 0 33.333%;
	max-width: 33.333%;
	width:100%;
	min-height:1px;
	border: 1px solid rgba(55,55,55,0.15);
	border-right: 0;
	font-weight: 500;
	font-size: 18px;
	line-height: 1.6667;
	padding: 85px 5% 45px;
	position: relative;
}
.vf-ref-step-number {
	position: absolute;
	left: 0;
	right: 0;
	margin: 0 auto;
	width: 110px;
	height: 110px;
	border-radius: 50%;
	background: #fff;
	border: 3px solid #dc2c5d;
	color: #dc2c5d;
	font-size: 64px;
	font-weight: 900;
	line-height: 100px;
	top: 0;
	transform: translateY(-50%);
	display: flex;
  justify-content: center;
  align-items: center;
}
.vf-ref-how-it-works h2 {
	margin: 0;
}
.vf-ref-prizes-section {
	text-align: center;
	padding: 95px 0 90px;
}
.vf-ref-prizes-section h2 {
	margin: 0 0 25px;
		color: #2cdcab;
}
.vf-ref-prizes-section p em {
	opacity: 0.75;
}
.vf-ref-price-cols {
	padding-top: 80px;
}
.vf-ref-price-col figure {
	margin: 0 0 40px;
}
.vf-ref-price-col img, .vf-ref-faq-img img {
	max-width: 100%;
	width: auto;
	height: auto;
}
.vf-ref-price-col h4 {
	margin: 0;
	text-align: center;
	font-size: 22px;
}
.vf-ref-sharing-section {
	background: #f4f6fa;
	padding: 30px 0;
	min-height: 467px;
	position: relative;
	display: flex;
  align-items: center;
}
.vf-ref-referrals-promotions .vf-ref-sharing-title {
	text-transform: uppercase;
	font-weight: 900;
	font-size: 70px;
	margin: 0 0 15px;
	/*background: -webkit-linear-gradient(#fd3162, #f9614a);
  	-webkit-background-clip: text;
  	-webkit-text-fill-color: transparent;*/
	color: #dc2c5d;
}
.vf-ref-sharing-info p {
	font-size: 20px;
	font-weight: 300;
	margin: 0;
}
.vf-ref-sharing-info span {
	font-size: 44px;
	font-weight: 500;
	display: inline-block;
	vertical-align: middle;
	margin-left: 4px;
	position: relative;
	top: -2px;
}
.vf-ref-sharing-img {
	margin: 0;
	position: absolute;
	left: 56%;
	/*bottom: -50px;*/
}
.vf-ref-faq-img {
	margin: 0;
	text-align: center;
}
.vf-ref-faq-section {
	padding: 70px 0 90px;
}
.vf-ref-faq-box h3 {
	font-size: 34px;
	margin: 0 0 40px;
}
.vf-ref-accordion-data {
	display: none;
	padding: 0 25px 25px;
	font-size: 15px;
	line-height: 1.6;
}
.vf-ref-accordion-data p {
	margin: 0;
}
.vf-ref-accordion-trigger {
	background: #f4f6fa;
	padding: 20px 60px 20px 25px;
	font-size: 18px;
	font-weight: 400;
	cursor: pointer;
	margin: 0 0 25px;
	position: relative;
}
.vf-ref-accordion-trigger-icon {
	position: absolute;
	right: 16px;
	top: 15px;
	width: 32px;
	height: 32px;
	border: 1px solid #2a2a2a;
	border-radius: 50%;
}
.vf-ref-accordion-trigger.vf-ref-open .vf-ref-accordion-trigger-icon:after {
	display: none;
}
.vf-ref-accordion-trigger-icon:before, .vf-ref-accordion-trigger-icon:after {
	content: '';
	background: #2a2a2a;
	width: 12px;
	height: 2px;
	margin: 0 auto;
	position: absolute;
	left: 1px;
	right: 0;
	top: 50%;
	margin-top: -1px;
}
.vf-ref-accordion-trigger-icon:after {
	transform: rotate(90deg);
}
.vf-ref-faq-cols {
  	align-items: center;
}
.vf-ref-legal-notice {
	font-size: 16px;
	line-height: 1.625;
	padding: 0 25px;
}
.vf-ref-legal-notice strong {
	color: #dc2c5d;
}
.vf-ref-campaign-btn {
	border: none;
	color: #fff;
	border-radius: 0;
	text-transform: uppercase;
	padding: 15px 25px;
	cursor: pointer;
	box-shadow: 0 6px 20px rgba(0,0,0,.18);
	position: relative;
	font-size: 24px;
	font-weight: 700;
	background: #dc2c5d;
	text-decoration: none;
	display: inline-block;
}
.vf-ref-campaign-btn:hover {
	opacity: 0.8;
  color: #ffffff;
}
.vf-ref-campaign-cta {
	padding: 100px 0;
	color: #fff;
	position: relative;
	z-index: 1;
	text-align: center;
	/*background: url(.vf-ref-.vf-ref-/images/happy-clients.vf-ref-jpg) no-repeat 50% 0;*/
	background-size: cover;
}
.vf-ref-campaign-cta:after {
	content: '';
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
	background: rgb(0,205,200);
	background: -moz-linear-gradient(top, rgba(0,205,200,1) 0%, rgba(44,221,155,1) 100%);
	background: -webkit-linear-gradient(top, rgba(0,205,200,1) 0%,rgba(44,221,155,1) 100%);
	background: linear-gradient(to bottom, rgba(0,205,200,1) 0%,rgba(44,221,155,1) 100%);
	filter: progid:DXImageTransform.vf-ref-Microsoft.vf-ref-gradient( startColorstr='#00cdc8', endColorstr='#2cdd9b',GradientType=0 );
	z-index: -1;
	opacity: 0.92;
}
.vf-ref-campaign-cta p {
	font-size: 36px;
	line-height: 1.vf-ref-3333;
	max-width: 600px;
	width: 100%;
	margin: 0 auto 35px;
	font-weight: 700;
}
.vf-ref-promotions-content {
	width: 57.5%;
}
.vf-ref-promotions-content-inner {
	max-width: 800px;
	width: 100%;
	float: right;
	padding: 20px;
}
.vf-ref-promotions-top-banner {
	display: flex;
	align-items: center;
	background: #f4f6fa;
	height: 820px;
	position: relative;
	z-index: 1;
	overflow: hidden;
}
.vf-ref-promotions-content-inner h1 {
	font-size: 70px;
	font-weight: 900;
	margin: 0 0 30px;
	line-height: 1.3;
}
.vf-ref-promotions-content-inner h1 span {
	display: block;
	font-size: 0.4571em;
	font-weight: 400;
}
.vf-ref-promotions-content-inner h1 span:after {
	content: '';
	display: inline-block;
	vertical-align: middle;
	width: 50px;
	height: 2px;
	background: #25d2b4;
	margin-left: 18px;
}
.vf-ref-promotions-content-inner p {
	text-transform: uppercase;
	font-size: 18px;
	line-height: 2;
	margin: 0 0 30px;
	width: 95%;
}
.vf-ref-promotions-img-col {
	/*background: url(.vf-ref-.vf-ref-/images/vf-ref-banner-hero.vf-ref-svg) no-repeat 0 0;*/
	background-position: 50% 0;
	background-size: cover;
	position: absolute;
	right: 0;
	top: 0;
	bottom: 0;
	width: 15%;
	z-index: -1;
	background: linear-gradient(to bottom, rgba(0,205,200,1) 0%,rgba(44,221,155,1) 100%);
}

.vf-ref-vf-ref-banner-hero {
	height: 100%;
  z-index: 9999;
  position: absolute;
  right: 50px;
}

@media screen and (max-width: 676px) {
	.vf-ref-vf-ref-banner-hero {
	position: absolute;
  right: -10px;
      height: 60%;
}
  .vf-ref-how-it-works {
    margin-top: -220px;
  }
}
.vf-ref-promotions-img-col:after {
	content: '';
	position: absolute;
	top: -100%;
	height: 200%;
	right: 100%;
	background: #f4f6fa;
	width: 400px;
	transform: rotate(15deg);
	transform-origin: 0 100%;
}
.vf-ref-promotions-img-col:before {
	content: '';
	position: absolute;
	width: 8px;
	height: 200%;
	top: -100%;
	transform: rotate(15deg) translateX(8px);
	transform-origin: 0 100%;
	background: rgb(0,205,200);
	background: -moz-linear-gradient(top, rgba(0,205,200,1) 0%, rgba(44,221,155,1) 100%);
	background: -webkit-linear-gradient(top, rgba(0,205,200,1) 0%,rgba(44,221,155,1) 100%);
	background: linear-gradient(to bottom, rgba(0,205,200,1) 0%,rgba(44,221,155,1) 100%);
	filter: progid:DXImageTransform.vf-ref-Microsoft.vf-ref-gradient( startColorstr='#00cdc8', endColorstr='#2cdd9b',GradientType=0 );
	z-index: 1;
}

@media only screen and (max-width: 1500px) {
	.vf-ref-promotions-top-banner {
		height: 700px;
	}
}
@media only screen and (max-width: 1400px) {
	.vf-ref-promotions-content-inner {
		padding: 30px;
	}
	.vf-ref-promotions-content-inner h1 {
		font-size: 60px;
		margin-bottom: 20px;
	}
	.vf-ref-promotions-content-inner p {
		font-size: 16px;
	}
	.vf-ref-promotions-top-banner {
		height: 650px;
	}
	.vf-ref-promotions-img-col {
		width: 22%;
	}
}
@media only screen and (max-width: 1200px) {
	.vf-ref-promotions-content-inner h1 {
		font-size: 46px;
	}
	.vf-ref-promotions-top-banner {
		height: 550px;
	}
	.vf-ref-referrals-promotions h2 {
		font-size: 40px;
	}
	.vf-ref-how-it-works {
		padding: 50px 0 60px;
	}
	.vf-ref-work-step {
		padding: 60px 25px 30px;
		font-size: 17px;
		line-height: 1.55;
	}
	.vf-ref-step-number {
		width: 80px;
		height: 80px;
		font-size: 48px;
		line-height: 70px;
	}
	.vf-ref-work-steps {
		margin-top: 70px;
	}
	.vf-ref-prizes-section {
		padding: 60px 0;
	}
	.vf-ref-prizes-section h2 {
		margin-bottom: 15px;
	}
	.vf-ref-price-col h4 {
		font-size: 20px;
	}
	.vf-ref-price-col figure {
		margin-bottom: 40px;
	}
	.vf-ref-price-col img {
		max-width: 90%;
		display: block;
		margin: 0 auto;
	}
	.vf-ref-price-cols {
		padding-top: 50px;
	}
	.vf-ref-referrals-promotions .vf-ref-sharing-title {
		font-size: 54px;
		margin-bottom: 10px;
	}
	.vf-ref-sharing-info p {
		font-size: 18px;
	}
	.vf-ref-sharing-info span {
		font-size: 36px;
	}
	.vf-ref-sharing-img img {
		max-height: 300px;
	}
	.vf-ref-sharing-img {
		bottom: -30px;
	}
	.vf-ref-sharing-section {
		min-height: 350px;
	}
	.vf-ref-faq-section {
		padding: 70px 0 50px;
	}
	.vf-ref-campaign-cta {
		padding: 70px 0;
	}
	.vf-ref-campaign-cta p {
		margin-bottom: 30px;
		font-size: 30px;
	}
}
@media only screen and (max-width: 996px) {
	.vf-ref-campaign-btn {
		font-size: 10px;
	}
	.vf-ref-promotions-content-inner h1 {
		font-size: 36px;
		margin-bottom: 15px;
	}
	.vf-ref-promotions-top-banner {
		height: 450px;
	}
	.vf-ref-promotions-content-inner p {
		font-size: 14px;
		width: auto;
		margin-bottom: 20px;
	}
	.vf-ref-referrals-promotions .vf-ref-sharing-title {
		font-size: 46px;
		margin: 0 0 5px;
	}
	.vf-ref-sharing-img img {
		max-height: 220px;
	}
	.vf-ref-sharing-img {
		left: 62%;
		bottom: -20px;
		display: none;
	}
	.vf-ref-sharing-section {
		min-height: 280px;
	}
	.vf-ref-faq-box h3 {
		font-size: 26px;
		margin-bottom: 25px;
	}
}
@media only screen and (max-width: 767px) {
	.vf-ref-promotions-img-col {
		position: static;
		height: 400px;
		width: auto;
		background-position: 50% 50%;
	}
	.vf-ref-promotions-img-col:after {
		display: none;
	}
	.vf-ref-promotions-top-banner {
		height: auto;
		display: block;

		background: transparent;
	}
	.vf-ref-promotions-content {
		display: table;
		width: 100%;

		position: inherit;
    top: -250px;
	}



	.vf-ref-promotions-content-inner h1 {
		color: #ffffff;
    text-shadow: 1px 1px 1px #202020;
	}

	.vf-ref-promotions-content-inner h1 span:after {
   background: #fff;
	}

	.vf-ref-promotions-content-inner strong {
		display: block;
		width: 30%;
	}
	.vf-ref-promotions-content-inner p {
		display: none;
	}
	.vf-ref-promotions-content-inner {
		padding: 35px 25px;
	}
	.vf-ref-work-step {
		-ms-flex: 0 0 100%;
		-webkit-flex: 0 0 100%;
		flex: 0 0 100%;
		max-width: 100%;
	}
	.vf-ref-step-number {
		width: 60px;
		height: 60px;
		font-size: 32px;
		line-height: 54px;
		border-width: 2px;
		left: 0;
		right: auto;
		top: 50%;
		margin-left: -30px;
		text-align: center;
	}
	.vf-ref-work-step {
		border-bottom: 0;
		text-align: left;
		padding: 25px 25px 25px 50px;
	}
	.vf-ref-work-steps {
		margin-left: 25px;
		margin-top: 30px;
		border-bottom: 1px solid rgba(55,55,55,0.15);
	}
	.vf-ref-price-col h4 {
		font-size: 17px;
	}
	.vf-ref-sharing-section {
		/*padding-right: 35%;*/
		min-height: 220px;
	}
	.vf-ref-referrals-promotions .vf-ref-sharing-title {
		font-size: 32px;
		line-height: 1.1;
		margin-bottom: 10px;
	}
	.vf-ref-sharing-info p {
		font-size: 16px;
	}
	.vf-ref-sharing-info span {
		font-size: 26px;
	}
	.vf-ref-grid-col {
		-ms-flex: 0 0 100%;
		-webkit-flex: 0 0 100%;
		flex: 0 0 100%;
		max-width: 100%;
	}
	.vf-ref-faq-img img {
		max-width: 300px;
		width: auto;
		height: auto;
		display: block;
		margin: 0 auto;
	}
	.vf-ref-faq-section {
		padding: 50px 0 30px;
	}
	.vf-ref-accordion-trigger {
		font-size: 16px;
		margin-bottom: 20px;
		padding: 15px 60px 15px 20px;
		line-height: 1.45;
	}
	.vf-ref-accordion-data {
		padding: 0 20px 20px;
	}
	.vf-ref-legal-notice {
		padding: 0 20px;
	}
	.vf-ref-campaign-cta {
		padding: 50px 0;
	}
	.vf-ref-campaign-cta p {
		font-size: 24px;
		margin-bottom: 25px;
	}
	.vf-ref-accordion-trigger-icon {
		top: 11px;
	}
	.vf-ref-promotions-img-col:before {
		display: none;
	}
}
@media only screen and (max-width: 575px) {
	.vf-ref-promotions-img-col {
		height: 300px;
	}
	.vf-ref-promotions-content-inner h1 {
		font-size: 30px;
	}
	.vf-ref-promotions-content-inner h1 span {
		font-size: 0.6em;
		margin-bottom: 5px;
	 }
	 .vf-ref-promotions-content-inner p {
		 font-size: 13px;
	 }
	 .vf-ref-referrals-promotions h2 {
		 font-size: 28px;
	 }
	 .vf-ref-prizes-section h2 {
		 margin-bottom: 5px;
	 }
	 .vf-ref-how-it-works {
		padding: 35px 0 40px;
	}
	.vf-ref-promotions-content-inner p {
		line-height: 1.7;
	}
	.vf-ref-work-step {
		font-size: 16px;
	}
	.vf-ref-prizes-section {
		padding: 40px 0;
	}
	.vf-ref-grid-cols3 .vf-ref-grid-col {
		flex: 0 0 100%;
		max-width: 100%;
	}
	.vf-ref-price-col img {
		max-width: 250px;
	}
	.vf-ref-price-cols {
		padding-top: 20px;
	}
	.vf-ref-price-col figure {
		margin-bottom: 30px;
	}
	.vf-ref-price-col h4 {
		font-size: 20px;
		margin-bottom: 10px;
	}
	.vf-ref-referrals-promotions .vf-ref-sharing-title {
		font-size: 26px;
	}
	.vf-ref-sharing-info p {
		font-size: 14px;
		line-height: 1.2;
	}
	.vf-ref-sharing-info span {
		font-size: 20px;
		margin: 0;
	}
	.vf-ref-sharing-img img {
		max-height: 180px;
	}
	.vf-ref-sharing-section {
		min-height: 190px;
	}
	.vf-ref-faq-box h3 {
		font-size: 22px;
	}
	.vf-ref-accordion-data, .vf-ref-legal-notice {
		font-size: 14px;
		line-height: 1.5;
	}
	.vf-ref-campaign-cta {
		padding: 35px 0;
	}
	.vf-ref-campaign-cta p {
		font-size: 20px;
		margin-bottom: 20px;
	}
}

.price-col {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: flex-end;
}
.price-col h4 {
  align-items: flex-end;
  height: 50px;
  width: 100%;
}



.vf-ref-price-img {
  width: 60%!important;
}

.vf-ref-price-img-1 {
	width: 50%!important;
}

.vf-ref-price-img-3 {
	width: 100%!important;
}

.vf-ref-price-img-4 {
	width: 90%!important;
}

.vf-ref-price-img-5 {
	width: 70%!important;
}






.vf-ref-faq-img img {
	width: 65%;
}

.vf-ref-sharing-img img {
	width: 240%;
}

.vf-ref-sharing-section .vf-ref-wrap {
	display: flex;
	align-items: center;
}



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
