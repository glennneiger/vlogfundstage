<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="x-dns-prefetch-control" content="on">
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="icon" href="https://www.vlogfund.com/wp-content/uploads/2018/06/V_logo_ICO.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="https://www.vlogfund.com/wp-content/uploads/2018/06/V_logo_ICO.ico" type="image/x-icon" />
	<?php wp_head(); ?>
	<?php if (!is_page('checkout')) : ?>
  <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
	<script>
	window.addEventListener("load", function(){
	window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "#fafafa"
			},
			"button": {
				"background": "#dc2c5d"
			}
		},
		"position": "bottom-left",
		"content": {
			"href": "https://www.vlogfund.com/privacy"
		}
	})
	});
	</script>
  <?php endif; //Endif ?>
</head>

<body <?php body_class(); ?>>
<?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?>
	<div class="container-fluid">
		<div class="ddl-full-width-row row header">
        	<div class="col-sm-12">
				<header id="masthead" class="site-header" role="banner">
					<nav class="sf-navigation" role="navigation">
						<ul class="sf-navigation-items sf-navigation-items-left">
							<li class="sf-navigation-item sf-hidden"><a id="create_campaign" href="/create-a-new-youtube-collaboration" class="sf-navigation-link sf-navigation-link-start hide-mobile">Submit a Collab</a></li>
							<li class="sf-navigation-item sf-hidden"><a href="/youtube-collaborations" class="sf-navigation-link hide-mobile"><i class="fa fa-search"></i> Collaborations</a></li>
							<li class="sf-navigation-item sf-hidden"><a href="/blog" class="sf-navigation-link hide-mobile"> Blog</a></li>
						</ul>
						<div class="sf-navigation-title sf-navigation-title-link sfc-brand-title"><span class="sfc-brand-title-inner"><a class="link link--ilin" href="/"><span>VLOG</span><span>FUND</span></a></span></div>
						<div class="sf-navigation-title sf-navigation-title-link sfc-brand-title sf-blog-only"><span class="sfc-brand-title-inner"><a class="link link--ilin" href="/"><span>Blog</span></a></span></div>
						<div id="sf-navigation-burger"><span></span><span></span><span></span></div>
						<ul id="sf-navigation-account-menu" class="sf-navigation-items sf-navigation-items-right sf-navigation-logged-in">
							<?php if( do_shortcode('[cart_count]') >= 1 ) : //Check Cart Count ?>
								<li class="sf-navigation-item sf-hidden"><a href="/checkout" class="sf-navigation-link"><i class="fa fa-user-plus"></i> <?php echo do_shortcode('[wpv-woo-cart-count]');?></a></li>
							<?php endif; //Endif
							if( is_user_logged_in() ) : //Check User Loggedin ?>
								<li class="sf-navigation-item"><a class="sf-navigation-link sf-navigation-dropdown-toggle"><i class="fa fa-user"></i> Account</a></li>
							<?php else : //Else ?>
								<li class="login-w-a sf-navigation-item sf-log-li"><a href="#login" class="sf-navigation-link">Login</a></li>
								<li class="login-w-a sf-navigation-item sf-reg-li"><a href="#register" class="sf-navigation-link">Register</a></li><li class="account-li hide login-w-a sf-navigation-item"><a class="sf-navigation-link sf-navigation-dropdown-toggle"><i class="fa fa-user"></i> Account</a></li>
							<?php endif; //Endif ?>
						</ul>
						<ul id="sf-navigation-account-items" class="sf-navigation-items sf-navigation-items-right sf-navigation-items-menu sf-navigation-logged-in sf-navigation-dropdown">
							 <li class="login-w-a sf-navigation-item sf-hidden"><a href="/" class="sf-navigation-link">About</a></li>
							<?php if( is_user_logged_in() ) : //Check User Logged In ?>
								<li class="sf-navigation-item sf-hidden"><a href="/account/edit-account" class="sf-navigation-link">My Account</a></li>
								<li class="sf-navigation-item sf-hidden"><a href="/account/my-campaigns" class="sf-navigation-link">My Campaigns</a></li>
								<li class="sf-navigation-item sf-hidden"><a href="/account/my-referrals" class="sf-navigation-link">My Referrals</a></li>
								<li class="sf-navigation-item sf-hidden"><?php echo do_shortcode('[wpv-logout-link redirect_url="'.add_query_arg('bye', 1, do_shortcode('[wpv-post-url]')).'" class="sf-navigation-link"]Sign Out[/wpv-logout-link]');?></li>
							<?php if( current_user_can('author') || current_user_can('administrator') ) : //Check Author ?>
								<li class="sf-navigation-item sf-hidden"><a href="/wp-admin" class="sf-navigation-link">Admin</a></li>
							<?php endif; //Endif
							else : //Else ?>
								<li class="account-li hide sf-navigation-item sf-hidden"><a href="/account/edit-account" class="sf-navigation-link">My Account</a></li>
								<li class="login-w-a sf-navigation-item sf-hidden"><a href="#login" class="sf-navigation-link">Login</a></li>
								<li class="login-w-a sf-navigation-item sf-hidden"><a href="#register" class="sf-navigation-link">Register</a></li>
							<?php endif; //Endif ?>
						</ul>
					</nav>
				</header>
			</div><!--/.container-fluid-->
		</div><!--/.ddl-full-width-row-->
	</div><!--/.container-fluid-->
	<!--Pop Ups-->
	<!--Regular Login-->
	<div id="login" class="sf-popup">
    	<div class="sf-popup-container">
			<span class="close"><a class="sf-popup-close" href="#"><i class="fas fa-times"></i></a></span>
        	<div class="sf-popup-head"><a href="#register"><span class="sf-popup-head-text">Don't have an account yet? Register</span></a></div>
      		<!--content-login-->
        	<div class="sf-popup-content">
				<h2>Login</h2>
				<?php /*<!--<div id="sf-popup-login-form-status" class="sf-popup-status"></div>-->
					<!--[woocommerce_social_login_buttons return_url="[wpv-post-url]?welc=1"]-->*/?>
			  	<?php echo do_shortcode('<div class="sf-popup-register-to-comment-h2">[woocommerce_social_login_buttons return_url="'.do_shortcode('[wpv-post-url]').'?welc_back=1#comment_login"]</div>');?>
			  	<?php echo do_shortcode('<div class="sf-popup-register-h2">[woocommerce_social_login_buttons return_url="'.add_query_arg('welc_back', 1, do_shortcode('[wpv-post-url]')).'"]</div>');?>
				<?php echo do_shortcode('<div class="sf-popup-register-to-create-a-campaign-h2">[woocommerce_social_login_buttons return_url="'.add_query_arg('welc_back', 1, do_shortcode('/create-a-new-youtube-collaboration')).'"]</div>');?>
			  	<?php echo do_shortcode('<div class="sf-popup-register-to-edit-campaign">[woocommerce_social_login_buttons return_url="/account/my-campaigns/"]</div>');?>
				<p class="sf-popup-line"><span> or </span></p>
			  	<span class="sf-email-login"><i class="fa fa-envelope"></i> Login with Email</span>
				<div class="sf-email-login-form hide">
					<?php echo do_shortcode('[my_form_shortcode]');?>
					<span><a href="/account/lost-password">Lost your password?</a></span>
				</div><!--/.sf-email-login-form-->
        	</div><!--/.sf-popup-content-->
    	</div><!--/.sf-popup-container-->
	</div><!--#login-->
	<!--Regular Register-->
	<div id="register" class="sf-popup">
		<div class="sf-popup-container">
			<span class="close"><a class="sf-popup-close" href="#"><i class="fas fa-times"></i></a></span>
			<div class="sf-popup-head"><a href="#login"><span class="sf-popup-head-text">Already have an account? Login here</span></a></div>
			<!--content-reg-->
			<div class="sf-popup-content">
				<h2 class="sf-popup-register-to-comment-h2">Register to comment</h2>
				<h2 class="sf-popup-register-h2">Register</h2>
				<h2 class="sf-popup-register-to-create-a-campaign-h2">Register to submit a collab</h2>
				<div class="sf-popup-register-to-comment-h2"><?php echo do_shortcode('[woocommerce_social_login_buttons return_url="'.do_shortcode('[wpv-post-url]').'?welc=1#comment_login"]');?></div>
				<div class="sf-popup-register-h2"><?php echo do_shortcode('[woocommerce_social_login_buttons return_url="'.add_query_arg('welc',1, do_shortcode('[wpv-post-url]')).'"]');?></div>
				<div class="sf-popup-register-to-create-a-campaign-h2"><?php echo do_shortcode('[woocommerce_social_login_buttons return_url="/create-a-new-youtube-collaboration?welc=1"]');?></div>
				<div class="sf-popup-register-to-edit-campaign"><?php echo do_shortcode('[woocommerce_social_login_buttons return_url="/account/my-campaigns/"]');?></div>
				<p class="sf-popup-line"><span> or </span></p>
				<span class="sf-email-registration"><i class="fa fa-envelope"></i> Register with Email</span>
				<div class="sf-email-registration-form hide"><?php echo do_shortcode('[registration_form_shortcode]');?></div>
			</div><!--/.sf-popup-content-->
		</div><!--/.sf-popup-container-->
	</div><!--/#register-->
	<div class="container-fluid">
		<div class="ddl-full-width-row row">
        	<div class="col-sm-12">
