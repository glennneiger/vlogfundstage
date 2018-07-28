<?php
/**
 * Template Name: Custom RSS Template - Feedname
 */
//$postCount = 5; // The number of posts to show in the feed
//$posts = query_posts('showposts=' . $postCount);
//query_posts( $args );
$args = array(
	'post_type' => 'product',
	'showposts' => 10,
	'post_status'=>'publish',
	'ignore_sticky_posts' => true,
);
$wp_query->query($args);
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
<channel>
	<title><?php bloginfo_rss('name'); ?> - YouTube Collaborations Feed</title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
<?php  while ($wp_query->have_posts()) :
	$wp_query->the_post();
	//$terms = wp_get_post_terms($post->ID, 'pwb-brand', array("fields" => "all"));
	//$terms = wp_get_post_terms( $post->ID, 'pwb-brand', array("fields" => "all") );
	//$brands=$terms[0];

	$custom_fields = get_post_custom($post->ID);
	//$brand=$brands->name;

	$price=$custom_fields['_price']['0'];
	$instock=$custom_fields['_stock_status']['0'];
	if($instock=="instock")
	{
		$instock="In stock";
	}
	$image1=$custom_fields['wpcf-collaborator-1-image']['0'];
	$image2=$custom_fields['wpcf-collaborator-2-image']['0'];

	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
	$thumb_url = $thumb['0'];
	?>
<item>
	<g:id><?php the_ID();?></g:id>
	<g:title><?php the_title_rss(); ?></g:title>
	<g:description><![CDATA[<?php the_excerpt_rss() ?>]]></g:description>
	<g:google_product_category>8</g:google_product_category>
	<g:link><?php the_permalink_rss(); ?></g:link>
	<?php if(!empty($image1)){?>
<g:image_link><?php echo $image1;?></g:image_link><?php }?>
	<?php if(!empty($image2)){?>

	<g:additional_image_link><?php echo $image2;?></g:additional_image_link><?php }?>
	<?php if(!empty($thumb_url)){?>

	<g:additional_image_link><?php echo $thumb_url;?></g:additional_image_link><?php }?>

	<g:brand>VlogFund></g:brand>
	<g:condition>New</g:condition>
	<g:availability><?php echo $instock;?></g:availability>
	<?php if(!empty($price)){?>
<g:price><?php echo $price.' USD'?></g:price>
	<?php }?><?php rss_enclosure(); ?><?php do_action('rss2_item');?>
</item>
<?php endwhile; ?>
</channel>
</rss>
