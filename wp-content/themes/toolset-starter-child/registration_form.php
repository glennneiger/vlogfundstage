<form id="register_user" class="ajax-auth"  action="register" method="post">
    <p class="status"></p>
    <input id="signonname" type="text" name="signonname" placeholder="Username" class="sf-popup-input required" required>
    <input id="email" type="text" class="sf-popup-input required email" placeholder="Email" name="email" autocomplete='email' required>
    <input id="signonpassword" type="password" class="sf-popup-input required" placeholder="Password" name="signonpassword" new-password required>
    <input type="password" id="password2" class="sf-popup-input required" placeholder="Repeat Password" name="password2" new-password required>
	<?php if( is_product() ) : //Check Product Page
		$categories = get_the_terms( $post->ID, 'product_cat' );
		$product_cats = array();
		foreach( $categories as $cat ) : $product_cats[] = $cat->name; endforeach; ?>
   		<input type="hidden" id="campaign" name="campaign" value="<?php echo ucwords( get_the_title() );?>">
    	<input type="hidden" id="camp_cat" name="camp_cat" value="<?php echo join(',', $product_cats);?>">
    <?php endif; //Endif ?>
    <input class="frm_final_submit submit_button" type="submit" value="SIGNUP">
</form>
