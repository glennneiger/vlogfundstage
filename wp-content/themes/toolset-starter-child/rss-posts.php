<?php
/**
 * Template Name: Vlogfund Blog Feed
 */
//$postCount = 5; // The number of posts to show in the feed
//$posts = query_posts('showposts=' . $postCount);
//query_posts( $args );
$args = array(
	'post_type' => 'post',
	'showposts' => 30,
	'post_status'=>'publish',
	'ignore_sticky_posts' => true,
);
$wp_query->query($args);
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:media="http://search.yahoo.com/mrss/">
<channel>
	<title><?php bloginfo_rss('name'); ?> - Posts Feed</title>
	<atom:link href="<?php bloginfo_rss('url') ?>" rel="self" type="application/rss+xml"/>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language>en-US</language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
<?php  while ($wp_query->have_posts()) :
	$wp_query->the_post();
	$custom_fields = get_post_custom($post->ID);

	$image1=$custom_fields['wpcf-post-youtube-video-id']['0'];

	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium' );
	$thumb_url = $thumb['0'];
	?>
<item>
	<title><?php the_title_rss(); ?></title>
	<link><?php the_permalink_rss(); ?></link>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
	<guid isPermaLink="false"><?php the_guid(); ?></guid>
	<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>

	<?php if(!empty($thumb_url)){?>
	<media:content url="<?php echo $thumb_url;?>" medium="image"/>
	<?php }?>
	<?php if(!empty($image1)){?>
		<media:content url="https://i.ytimg.com/vi/<?php echo $image1;?>/hqdefault.jpg" medium="image"/>
		<?php }?>
<?php rss_enclosure(); ?>
<?php do_action('rss2_item');?>
</item>
<?php endwhile; ?>
</channel>
</rss>
