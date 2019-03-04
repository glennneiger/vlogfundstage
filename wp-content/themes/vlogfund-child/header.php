<?php
$cart_count = do_shortcode( '[cart_count]' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="profile" href="http://gmpg.org/xfn/11"/>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?>




<header id="masthead" class="site-header" role="banner">
    <nav class="sf-navigation" role="navigation">

<ul class="sf-navigation-items sf-navigation-items-left">


<li class="sf-navigation-item sf-hidden"><a id="create_campaign" href="/create-a-new-youtube-collaboration" class="sf-navigation-link sf-navigation-link-start hide-mobile">Submit a Collab</a></li>
<li class="sf-navigation-item sf-hidden"><a href="/youtube-collaborations" class="sf-navigation-link hide-mobile"><i class="fa fa-search"></i> Collaborations</a></li>
<li class="sf-navigation-item sf-hidden"><a href="/blog" class="sf-navigation-link hide-mobile"> Blog</a></li>
[wpv-conditional if="( vlogfund_smile_mode_on() eq '1' )"]<!--<li class="sf-navigation-item sf-hidden hide-mobile"><a href="/organization" class="sf-navigation-link">Causes</a></li>-->[/wpv-conditional]
</ul>

      <div class="sf-navigation-title sf-navigation-title-link sfc-brand-title"><span class="sfc-brand-title-inner"><a class="link link--ilin" href="/"><span>VLOG</span><span>FUND</span></a></span></div>
      <div class="sf-navigation-title sf-navigation-title-link sfc-brand-title sf-blog-only"><span class="sfc-brand-title-inner"><a class="link link--ilin" href="/"><span>Blog</span></a></span></div>

       <div id="sf-navigation-burger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <ul id="sf-navigation-account-menu" class="sf-navigation-items sf-navigation-items-right sf-navigation-logged-in">

          [wpv-conditional if="( '[cart_count]' gte '1' )"]
          <li class="sf-navigation-item sf-hidden"><a href="/checkout" class="sf-navigation-link"><i class="fa fa-user-plus"></i> <?php echo do_shortcode ('[wpv-woo-cart-count]') ?></a></li>
          [/wpv-conditional]
          [wpv-conditional if="( '[wpv-current-user info='logged_in']' eq 'true' )"]
            <li class="sf-navigation-item">
              <a class="sf-navigation-link sf-navigation-dropdown-toggle"><i class="fa fa-user"></i> Account</a>
            </li>
          [/wpv-conditional]
          [wpv-conditional if="( '[wpv-current-user info='logged_in']' eq 'false' )"]
          <li class="login-w-a sf-navigation-item sf-log-li"><a href="#login" class="sf-navigation-link">Login</a></li>
          <li class="login-w-a sf-navigation-item sf-reg-li"><a href="#register" class="sf-navigation-link">Register</a></li>
          <li class="account-li hide login-w-a sf-navigation-item">
            <a class="sf-navigation-link sf-navigation-dropdown-toggle"><i class="fa fa-user"></i> Account</a>
          </li>
          [/wpv-conditional]
        </ul>

        <ul id="sf-navigation-account-items" class="sf-navigation-items sf-navigation-items-right sf-navigation-items-menu sf-navigation-logged-in sf-navigation-dropdown">



          <!--<li class="sf-navigation-item sf-hidden"><a href="/account/my-donations" class="sf-navigation-link">My Donations</a></li>-->

          [wpv-conditional if="( '[wpv-current-user info='logged_in']' eq 'true' )"]
            <li class="sf-navigation-item sf-hidden"><a href="/account/edit-account" class="sf-navigation-link">My Account</a></li>
            <li class="sf-navigation-item sf-hidden"><a href="/account/my-campaigns" class="sf-navigation-link">My Campaigns</a></li>
            <li class="sf-navigation-item sf-hidden">[wpv-logout-link redirect_url="[wpv-post-url]?bye=1" class="sf-navigation-link"]Sign Out[/wpv-logout-link]</li>
          [/wpv-conditional]
          [wpv-conditional if="( '[wpv-current-user info='role']' eq 'author' )"]
         <li class="sf-navigation-item sf-hidden"><a href="/wp-admin" class="sf-navigation-link">Admin</a></li>
          [/wpv-conditional]
          [wpv-conditional if="( '[wpv-current-user info='logged_in']' eq 'false' )"]
          <li class="account-li hide sf-navigation-item sf-hidden"><a href="/account/edit-account" class="sf-navigation-link">My Account</a></li>
          <li class="login-w-a sf-navigation-item sf-hidden"><a href="#login" class="sf-navigation-link">Login</a></li>
          <li class="login-w-a sf-navigation-item sf-hidden"><a href="#register" class="sf-navigation-link">Register</a></li>
          [/wpv-conditional]
        </ul>    </nav>
</header>






<!--Pop Ups-->

<!--Regular Login-->

<div id="login" class="sf-popup">
    <div class="sf-popup-container">
      <span class="close"><a class="sf-popup-close" href="#"><i class="fas fa-times"></i></a></span>

        <div class="sf-popup-head">
            <a href="#register"><span class="sf-popup-head-text">Don't have an account yet? Register</span></a>
        </div>

      <!--content-login--->
        <div class="sf-popup-content">
            <h2>Login</h2>
            <!--<div id="sf-popup-login-form-status" class="sf-popup-status"></div>-->

          <!--[woocommerce_social_login_buttons return_url="[wpv-post-url]?welc=1"]-->

          <div class="sf-popup-register-to-comment-h2">[woocommerce_social_login_buttons return_url="[wpv-post-url]?welc_back=1#comment_login"]</div>
          <div class="sf-popup-register-h2">[woocommerce_social_login_buttons return_url="[wpv-post-url]?welc_back=1"]</div>
            <div class="sf-popup-register-to-create-a-campaign-h2">[woocommerce_social_login_buttons return_url="/create-a-new-youtube-collaboration?welc_back=1"]</div>
          <div class="sf-popup-register-to-edit-campaign">[woocommerce_social_login_buttons return_url="/account/my-campaigns/"]</div>

            <p class="sf-popup-line">
                <span> or </span>
            </p>


          <span class="sf-email-login"><i class="fa fa-envelope"></i> Login with Email</span>

          <!--shortcode-->
          <div class="sf-email-login-form hide">
            [my_form_shortcode]
          <span><a href="/account/lost-password">Lost your password?</a></span>
          </div>
          <!--shortcode-->

        </div>


    </div>
</div>


<!--Regular Register-->


<div id="register" class="sf-popup">
    <div class="sf-popup-container">
        <span class="close"><a class="sf-popup-close" href="#"><i class="fas fa-times"></i></a></span>

        <div class="sf-popup-head">
            <a href="#login"><span class="sf-popup-head-text">Already have an account? Login here</span></a>
        </div>

      <!--content-reg--->
        <div class="sf-popup-content">
            <h2 class="sf-popup-register-to-comment-h2">Register to comment</h2>
            <h2 class="sf-popup-register-h2">Register</h2>
            <h2 class="sf-popup-register-to-create-a-campaign-h2">Register to submit a collab</h2>


          <div class="sf-popup-register-to-comment-h2">[woocommerce_social_login_buttons return_url="[wpv-post-url]?welc=1#comment_login"]</div>
          <div class="sf-popup-register-h2">[woocommerce_social_login_buttons return_url="[wpv-post-url]?welc=1"]</div>
            <div class="sf-popup-register-to-create-a-campaign-h2">[woocommerce_social_login_buttons return_url="/create-a-new-youtube-collaboration?welc=1"]</div>
          <div class="sf-popup-register-to-edit-campaign">[woocommerce_social_login_buttons return_url="/account/my-campaigns/"]</div>

            <p class="sf-popup-line">
                <span> or </span>
            </p>

         <span class="sf-email-registration"><i class="fa fa-envelope"></i> Register with Email</span>

          <!--shortcode-->
          <div class="sf-email-registration-form hide">
          [registration_form_shortcode]
          </div>
          <!--shortcode-->

        </div>

    </div>
</div>


	<section class="<?php echo (get_theme_mod('ref_container_wrapper', 1) == 1)? 'container' : '';?> container-main" role="main">
