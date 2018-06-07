<?php // Template for my form shortcode ?>
<form id="login" action="login" method="post">
        <p class="status"></p>
        <!--<label for="username">Username or email</label>-->
        <input id="username" type="text" name="username" class="sf-popup-input" placeholder="Username or email" required>
        <!--<label for="password">Password</label>-->
        <input id="password" type="password" name="password" class="sf-popup-input" placeholder="Enter password" required>
        <!--<a class="lost" href="<?php echo wp_lostpassword_url(); ?>">Lost your password?</a>-->
        <input class="frm_final_submit" type="submit" value="Login" name="submit">
        <!--<?php wp_nonce_field( 'ajax-login-nonce', 'security_login' ); ?>-->
    </form>
