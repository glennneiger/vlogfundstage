<?php
/**
 * Template Name: YouTube Collaboration Campaigns Template - Feedname
 */
//$postCount = 5; // The number of posts to show in the feed
//$posts = query_posts('showposts=' . $postCount);
//query_posts( $args );
$args = array(
	'post_type' => 'product',
	'showposts' => 30,
	'post_status'=>'publish',
	'ignore_sticky_posts' => true,
);
$wp_query->query($args);
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<listings>
	<title><?php bloginfo_rss('name'); ?> - YouTube Collaboration Campaign Feed</title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
<?php  while ($wp_query->have_posts()) :
	$wp_query->the_post();
	$custom_fields = get_post_custom($post->ID);

	$image1=$custom_fields['wpcf-collaborator-1-image']['0'];
	$image2=$custom_fields['wpcf-collaborator-2-image']['0'];

	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
	$thumb_url = $thumb['0'];
	?>
<listing>
	<destination_id><?php the_ID();?></destination_id>
	<name><?php the_title_rss(); ?></name>
	<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
	<address format="simple">
          <component name="addr1">YouTuber Street</component>
          <component name="city">YouTube</component>
					<component name="region">Dream</component>
          <component name="country">United States</component>
  </address>
	<?php if(!empty($thumb_url)){?>
	<image>
				 <url><?php echo $thumb_url;?></url>
	</image><?php }?>
	<?php if(!empty($image1)){?>
	<image>
					<url><?php echo $image1;?></url>
	 </image><?php }?>
	 <?php if(!empty($image2)){?>
	 <image>
					<url><?php echo $image2;?></url>
	 </image><?php }?>
	 <type>collaboration</type>
	 <url><?php the_permalink_rss(); ?></url>
<?php rss_enclosure(); ?><?php do_action('rss2_item');?>
</listing>
<?php endwhile; ?>
</listings>
