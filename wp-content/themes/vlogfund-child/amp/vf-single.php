<?php
/**
 * Single view template.
 *
 * @package AMP
 */

/**
 * Context.
 *
 * @var AMP_Post_Template $this
 */
global $post;
$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
$thumb_url = $thumb['0'];
$this->load_parts( array( 'html-start' ) );
?>
<amp-analytics type="googleanalytics" id="googleanalytics">
<script type="application/json">
{
"vars": {
  "account": "UA-112714369-1"
},
"triggers": {
 "track pageview": {
  "on": "visible",
  "request": "pageview"
},
"trackClickOnRelatedArticle": {
   "selector": "#amp-related-article",
   "on": "click",
   "request": "event",
   "vars": {
     "eventCategory": "AMP",
     "eventAction": "Related Article AMP",
     "eventLabel": "Related Article Click AMP"
    }
  },
  "trackClickOnCommentsLink" : {
    "on": "click",
    "selector": ".amp-wp-comments-link",
    "request": "event",
    "vars": {
      "eventCategory": "AMP",
      "eventAction": "Comment Link AMP",
      "eventLabel": "Comment Link Click AMP"
    }
  },
  "trackClickOnTwitterLink" : {
    "on": "click",
    "selector": "#twitter",
    "request": "social",
    "vars": {
        "socialNetwork": "Twitter",
        "socialAction": "Share",
        "socialTarget": "https://www.vlogfund.com"
    }
  },
    "trackClickOnFacebookLink" : {
      "on": "click",
      "selector": "#facebook",
      "request": "social",
      "vars": {
          "socialNetwork": "Facebook",
          "socialAction": "Share",
          "socialTarget": "https://www.vlogfund.com"
      }
    },
      "trackClickOnEmailLink" : {
        "on": "click",
        "selector": "#email",
        "request": "social",
        "vars": {
            "socialNetwork": "Email",
            "socialAction": "Share",
            "socialTarget": "https://www.vlogfund.com"
        }
      },
        "trackClickOnGoogleLink" : {
          "on": "click",
          "selector": "#google",
          "request": "social",
          "vars": {
              "socialNetwork": "Google",
              "socialAction": "Share",
              "socialTarget": "https://www.vlogfund.com"
          }
        },
          "trackClickOnWhatsappLink" : {
            "on": "click",
            "selector": "#whatsapp",
            "request": "social",
            "vars": {
                "socialNetwork": "Whatsapp",
                "socialAction": "Share",
                "socialTarget": "https://www.vlogfund.com"
            }
          },
            "trackClickOnMessengerLink" : {
              "on": "click",
              "selector": "#messenger",
              "request": "social",
              "vars": {
                  "socialNetwork": "Messenger",
                  "socialAction": "Share",
                  "socialTarget": "https://www.vlogfund.com"
              }
            },
              "trackClickOnSmsLink" : {
                "on": "click",
                "selector": "#sms",
                "request": "social",
                "vars": {
                    "socialNetwork": "Sms",
                    "socialAction": "Share",
                    "socialTarget": "https://www.vlogfund.com"
                }
              }
}
}
</script>
</amp-analytics>
<amp-sidebar id="sidebar1" layout="nodisplay" side="right">
  <ul>
    <li class="vf-sidebar-close"><button on='tap:sidebar1.close'>x</button></li>
    <li><a href="/about">About</a></li>
    <!--<li><a href="#idTwo" on="tap:idTwo.scrollTo"></a></li>-->
    <li><a href="/campaign-form">Create a Collab</a></li>
    <li><a href="/youtube-collaborations"><i class="fa fa-search"></i> Explore</a></li>
    <li><a href="/youtube-collaborations">YouTube Collaborations</a></li>
    <li><a href="/account">Login</a></li>
    <li><a href="/faq">FAQ</a></li>
  </ul>
</amp-sidebar>

<button class="vf-hamburger" on='tap:sidebar1.toggle'>&#9776;</button>




<?php $this->load_parts( array( 'header' ) ); ?>

<article class="amp-wp-article">
	<header class="amp-wp-article-header">
		<h1 class="amp-wp-title"><?php echo esc_html( $this->get( 'post_title' ) ); ?></h1>

		<?php $this->load_parts( apply_filters( 'amp_post_article_header_meta', array( 'meta-author', 'meta-time' ) ) ); ?>

	</header>


	<?php $this->load_parts( array( 'featured-image' ) ); ?>

<?php if(empty($thumb_url)){?>
  <amp-img alt="thumbnail"
    src="https://i.ytimg.com/vi/<?php echo get_post_meta( $post->ID, 'wpcf-post-youtube-video-id', true); ?>/hqdefault.jpg"
    width="900"
    height="675"
    layout="responsive">
  </amp-img>
<?php }?>

	<div class="amp-wp-article-content">
		<?php echo $this->get( 'post_amp_content' ); // WPCS: XSS ok. Handled in AMP_Content::transform(). ?>

    <amp-social-share id="email" width="40" height="30" type="email"></amp-social-share>
  <amp-social-share id="facebook" width="40" height="30" type="facebook"
    data-param-app_id="181038895828102"></amp-social-share>
  <amp-social-share id="google" width="40" height="30" type="gplus"></amp-social-share>
  <amp-social-share id="twitter" width="40" height="30" type="twitter"></amp-social-share>
  <amp-social-share id="whatsapp" width="40" height="30" type="whatsapp"></amp-social-share>
  <amp-social-share id="sms" width="40" height="30" type="sms"></amp-social-share>
  <amp-social-share id="messenger" width="40" height="30" type="facebookmessenger"
    data-share-endpoint="fb-messenger://share"
    data-param-text="Check out this article: TITLE - CANONICAL_URL">
</amp-social-share>
	</div>

	<footer class="amp-wp-article-footer">
		<?php $this->load_parts( apply_filters( 'amp_post_article_footer_meta', array( 'meta-taxonomy', 'meta-comments-link' ) ) ); ?>
	</footer>
</article>

<article class="amp-wp-article">
<?php $this->load_parts( array( 'related-posts' ) ); ?>
</article>

<a href="#top" class="back-to-top"><?php esc_html_e( 'Back to top', 'amp' ); ?></a>
<?php $this->load_parts( array( 'footer' ) ); ?>

<?php
$this->load_parts( array( 'html-end' ) );
