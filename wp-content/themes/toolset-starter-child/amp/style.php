<?php
/**
 * Style template.
 *
 * @package AMP
 */

/**
 * Context.
 *
 * @var AMP_Post_Template $this
 */

$content_max_width       = absint( $this->get( 'content_max_width' ) );
$theme_color             = $this->get_customizer_setting( 'theme_color' );
$text_color              = $this->get_customizer_setting( 'text_color' );
$muted_text_color        = $this->get_customizer_setting( 'muted_text_color' );
$border_color            = $this->get_customizer_setting( 'border_color' );
$link_color              = $this->get_customizer_setting( 'link_color' );
$header_background_color = $this->get_customizer_setting( 'header_background_color' );
$header_color            = $this->get_customizer_setting( 'header_color' );
?>
/* Generic WP styling */

.alignright {
	float: right;
}

.alignleft {
	float: left;
}

.aligncenter {
	display: block;
	margin-left: auto;
	margin-right: auto;
}

.amp-wp-enforced-sizes {
	/** Our sizes fallback is 100vw, and we have a padding on the container; the max-width here prevents the element from overflowing. **/
	max-width: 100%;
	margin: 0 auto;
  width: 100%;
}

.amp-wp-unknown-size img {
	/** Worst case scenario when we can't figure out dimensions for an image. **/
	/** Force the image into a box of fixed dimensions and use object-fit to scale. **/
	object-fit: contain;
}

/* Template Styles */

.amp-wp-content,
.amp-wp-title-bar div {
	<?php if ( $content_max_width > 0 ) : ?>
	margin: 0 auto;
	max-width: <?php echo sprintf( '%dpx', $content_max_width ); ?>;
	<?php endif; ?>
}

html {
	background: <?php echo sanitize_hex_color( $header_background_color ); ?>;
}

body {
	/*background: <?php echo sanitize_hex_color( $theme_color ); ?>;*/
  background-color: #fafafa;
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
	font-family: -apple-system,BlinkMacSystemFont,SegoeUI, "Helvetica Neue", Helvetica, sans-serif;
	font-weight: 300;
	line-height: 1.75em;
}

p,
ol,
ul,
figure {
	margin: 0 0 1em;
	padding: 0;
  list-style-type: none;
}

a,
a:visited {
	/*color: <?php echo sanitize_hex_color( $link_color ); ?>;*/
  color: #6b10d6;
}

a:hover,
a:active,
a:focus {
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
}

/* Quotes */

blockquote {
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
	background: rgba(127,127,127,.125);
	border-left: 2px solid <?php echo sanitize_hex_color( $link_color ); ?>;
	margin: 8px 0 24px 0;
	padding: 16px;
}

blockquote p:last-child {
	margin-bottom: 0;
}

/* UI Fonts */

.amp-wp-meta,
.amp-wp-header div,
.amp-wp-title,
.wp-caption-text,
.amp-wp-tax-category,
.amp-wp-tax-tag,
.amp-wp-comments-link,
.amp-wp-footer p,
.back-to-top {
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen-Sans", "Ubuntu", "Cantarell", "Helvetica Neue", sans-serif;
}

/* Header */

.amp-wp-header {
	/*background-color: <?php echo sanitize_hex_color( $header_background_color ); ?>;*/
  background-color: #ffffff;
  box-shadow: 0 4px 10px 0 rgba(22, 22, 22, 0.08);
}

.amp-wp-header div {
	color: <?php echo sanitize_hex_color( $header_color ); ?>;
	font-size: 1em;
	font-weight: 400;
	margin: 0 auto;
	max-width: calc(840px - 32px);
	padding: .875em 16px;
	position: relative;
}

.amp-wp-header a {
	color: <?php echo sanitize_hex_color( $header_color ); ?>;
	text-decoration: none;
}

/* Sidebar */
.vf-hamburger {
  position: absolute;
  top: 18px;
  right: 18px;
  font-size: 22px;
  background-color: transparent;
  border: none;
  cursor: pointer;
  z-index: 999;
}

amp-sidebar {
  background-color: #202020;
  padding: 15px;
}

amp-sidebar ul li {
  color: #ffffff;
  max-width: 180px;
  width: 180px;
  padding: 5px 0;
}
amp-sidebar ul li a, amp-sidebar ul li a:visited {
  color: #ffffff;
  text-decoration: none;
}
.vf-sidebar-close {
  text-align: right;
}

.vf-sidebar-close button {
  color: white;
background: transparent;
border: navajowhite;
font-size: 14px;
font-weight: bold;
cursor: pointer;
}

/* Site Icon */

.amp-wp-header amp-img.amp-wp-site-icon {
	/** site icon is 32px **/
	/*background-color: <?php echo sanitize_hex_color( $header_color ); ?>;
	border: 1px solid <?php echo sanitize_hex_color( $header_color ); ?>;
	border-radius: 50%;
	position: absolute;
	right: 18px;
	top: 10px;*/
  margin: auto;
  width: 200px;
  height: 40px;
  display: block;
}

/* Site Title */

.amp-site-title {
  display: none;
}

/* Article */

.amp-wp-article {
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
	font-weight: 400;
	margin: 1.5em auto;
	max-width: 840px;
	overflow-wrap: break-word;
	word-wrap: break-word;
  box-shadow: 0 0 4px 0 #dadddd;
  background-color: #ffffff;
  padding: 5px 0;
}

/* Article Header */

.amp-wp-article-header {
	align-items: center;
	align-content: stretch;
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	margin: 1.5em 16px 0;
}

.amp-wp-title {
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
	display: block;
	flex: 1 0 100%;
	font-weight: 900;
	margin: 0 0 .625em;
	width: 100%;
}

/* Article Meta */

.amp-wp-meta {
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	display: inline-block;
	flex: 2 1 50%;
	font-size: .875em;
	line-height: 1.5em;
	margin: 0 0 1.5em;
	padding: 0;
  /*display: none;*/
}

.amp-wp-article-header .amp-wp-meta:last-of-type {
	text-align: right;
}

.amp-wp-article-header .amp-wp-meta:first-of-type {
	text-align: left;
}

.amp-wp-byline amp-img,
.amp-wp-byline .amp-wp-author {
	display: inline-block;
	vertical-align: middle;
}

.amp-wp-byline amp-img {
	border: 1px solid <?php echo sanitize_hex_color( $link_color ); ?>;
	border-radius: 50%;
	position: relative;
	margin-right: 6px;
}

.amp-wp-posted-on {
	text-align: right;
}

/* Featured image */

.amp-wp-article-featured-image {
	margin: 0 0 1em;
}
.amp-wp-article-featured-image amp-img {
	margin: 0 auto;
}
.amp-wp-article-featured-image.wp-caption .wp-caption-text {
	margin: 0 18px;
}

/* Article Content */

.amp-wp-article-content {
	margin: 0 16px;
}

.amp-wp-article-content ul,
.amp-wp-article-content ol {
	margin-left: 1em;
}

.amp-wp-article-content amp-img {
	margin: 0 auto;
}

.amp-wp-article-content amp-img.alignright {
	margin: 0 0 1em 16px;
}

.amp-wp-article-content amp-img.alignleft {
	margin: 0 16px 1em 0;
}

/* Captions */

.wp-caption {
	padding: 0;
}

.wp-caption.alignleft {
	margin-right: 16px;
}

.wp-caption.alignright {
	margin-left: 16px;
}

.wp-caption .wp-caption-text {
	border-bottom: 1px solid <?php echo sanitize_hex_color( $border_color ); ?>;
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	font-size: .875em;
	line-height: 1.5em;
	margin: 0;
	padding: .66em 10px .75em;
}

/* AMP Media */

amp-carousel {
	background: <?php echo sanitize_hex_color( $border_color ); ?>;
	margin: 0 -16px 1.5em;
}
amp-iframe,
amp-youtube,
amp-instagram,
amp-vine {
	background: <?php echo sanitize_hex_color( $border_color ); ?>;
	margin: 0 -16px 1.5em;
}

.amp-wp-article-content amp-carousel amp-img {
	border: none;
}

amp-carousel > amp-img > img {
	object-fit: contain;
}

.amp-wp-iframe-placeholder {
	background: <?php echo sanitize_hex_color( $border_color ); ?> url( <?php echo esc_url( $this->get( 'placeholder_image_url' ) ); ?> ) no-repeat center 40%;
	background-size: 48px 48px;
	min-height: 48px;
}

/* Article Footer Meta */

.amp-wp-article-footer .amp-wp-meta {
	display: block;
}

.amp-wp-tax-category,
.amp-wp-tax-tag {
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	font-size: .875em;
	line-height: 1.5em;
	margin: 1.5em 16px;
}

.amp-wp-comments-link {
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	font-size: .875em;
	line-height: 1.5em;
	text-align: center;
	margin: 2.25em 0 1.5em;
}

.amp-wp-comments-link a {
	border-style: solid;
	border-color: <?php echo sanitize_hex_color( $border_color ); ?>;
	border-width: 1px 1px 2px;
	border-radius: 0;
	background-color: transparent;
	/*color: <?php echo sanitize_hex_color( $link_color ); ?>;*/
  color: #202020;
	cursor: pointer;
	display: block;
	font-size: 14px;
	font-weight: 600;
	line-height: 18px;
	margin: 0 auto;
	max-width: 200px;
	padding: 11px 16px;
	text-decoration: none;
	width: 50%;
	-webkit-transition: background-color 0.2s ease;
			transition: background-color 0.2s ease;
}

/* AMP Footer */

.sf-footer-social {
    margin: 0 auto;
    text-align: center;
    box-shadow: inset 0 16px 20px -12px black;
    padding: 10px 0 15px 2px;
}

.sf-footer-social-link {
    vertical-align: middle;
}

.sf-footer-social-link .fa {
    height: 50px;
    width: 50px;
    line-height: 50px;
    background-color: #6b10d6;
    color: #041913;
}

.sf-footer-social-link .fa:hover {
    opacity: 0.8;
}

.amp-wp-footer {
	/*border-top: 1px solid <?php echo sanitize_hex_color( $border_color ); ?>;*/
	margin: calc(1.5em - 1px) 0 0;
  background-color: #202020;
  box-shadow: inset 0 16px 20px -12px black;
}

.amp-wp-footer div {
	margin: 0 auto;
	max-width: calc(840px - 32px);
	padding: 1.25em 16px 1.25em;
	position: relative;
}

.amp-wp-footer h2 {
	font-size: 1em;
	line-height: 1.375em;
	margin: 0 0 .5em;
  color: #ffffff;
}

.amp-wp-footer p {
	/*color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;*/
	font-size: .8em;
	line-height: 1.5em;
	margin: 0 85px 0 0;
}

.amp-wp-footer a {
	text-decoration: none;
  color: #ffffff;
}

.back-to-top {
  display: flex;
  justify-content: center;
	font-size: .8em;
	font-weight: 600;

}

.sf-footer-legal {
  display: flex;
  flex-direction: row;
  justify-content: center;
  padding: 0;
}
.sf-footer-legal a {
font-size: 12px;
}
.sf-footer-end {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    background-color: #1b1b1b;
    padding: 0;
}

.sf-footer-copyright {
    margin: 20px;
    color: #999;
    font-size: 14px;
}

/* Youtube */
amp-youtube iframe {
  display: none;
}

body article.amp-wp-article div.amp-wp-article-content amp-youtube img.amp-hidden {
visibility: visible;
}

amp-iframe .amp-wp-iframe-placeholder.amp-hidden {
visibility: hidden;
}

/* share */

body article.amp-wp-article amp-social-share {
  width: 40px;
  height: 30px;
}
