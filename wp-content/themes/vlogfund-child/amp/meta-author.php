<?php $post_author = $this->get( 'post_author' ); ?>
<?php if ( $post_author ) : ?>

	<div class="amp-wp-meta amp-wp-byline">
  <amp-social-share id="facebook" width="40" height="30" type="facebook"
    data-param-app_id="181038895828102"></amp-social-share>
  <amp-social-share id="twitter" width="40" height="30" type="twitter"></amp-social-share>
  <amp-social-share id="messenger" width="40" height="30" type="facebookmessenger"
    data-share-endpoint="fb-messenger://share"
    data-param-text="Check out this article: TITLE - CANONICAL_URL">
</amp-social-share>
	</div>
<?php endif; ?>
